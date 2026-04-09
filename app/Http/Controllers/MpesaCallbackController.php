<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\Voucher;
use App\Models\TelegramAdmin;
use App\Notifications\PaymentSucceeded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Mpesa callback', $data);

        $stk = $data['Body']['stkCallback'] ?? null;
        if (! $stk) {
            Log::warning('Mpesa callback: missing stkCallback');
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'OK'], 200);
        }

        $resultCode       = $stk['ResultCode'] ?? null;
        $resultDescFromCb = $stk['ResultDesc'] ?? null;

        // Extract metadata from callback
        $callbackMeta = collect($stk['CallbackMetadata']['Item'] ?? []);

        $amountItem  = $callbackMeta->firstWhere('Name', 'Amount');
        $receiptItem = $callbackMeta->firstWhere('Name', 'MpesaReceiptNumber');
        $phoneItem   = $callbackMeta->firstWhere('Name', 'PhoneNumber');

        $amount  = $amountItem['Value']  ?? null;
        $receipt = $receiptItem['Value'] ?? null;
        $phone   = $phoneItem['Value']   ?? null;

        Log::info('Mpesa callback meta extracted', [
            'amount'  => $amount,
            'receipt' => $receipt,
            'phone'   => $phone,
        ]);

        // Find matching pending payment by phone + amount
        $payment = Payment::where('phone', $phone)
            ->where('amount', $amount)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (! $payment) {
            Log::warning('Mpesa callback: payment not found for phone/amount', [
                'phone'  => $phone,
                'amount' => $amount,
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Payment record not found, but callback processed',
            ], 200);
        }

        // Use ResultDesc from this callback; if missing, leave null for now
        $resultDescFinal = $resultDescFromCb;

        $status = ((int) $resultCode === 0) ? 'successful' : 'failed';

        $payment->update([
            'status'             => $status,
            'reference'          => $receipt,
            'amount'             => $amount,
            'phone'              => $phone,
            'result_description' => $resultDescFinal,
            'payload'            => $data, // store full callback
        ]);

        Log::info('Mpesa callback: payment updated', [
            'payment_id' => $payment->id,
            'status'     => $status,
            'receipt'    => $receipt,
        ]);

        // After updating, generate voucher if this payment is successful
        $this->handleSuccessfulPayment($payment);

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'OK'], 200);
    }

    protected function mapPlanByAmount(Payment $payment): array
    {
        $amount = (int) $payment->amount;

        return match ($amount) {
            10 => [
                'profile_id'  => 2,          // 40 min profile
                'valid_hours' => 1,
            ],
            20 => [
                'profile_id'  => 3,          // 2 hours profile
                'valid_hours' => 2,
            ],
            80 => [
                'profile_id'  => 4,          // daily profile
                'valid_hours' => 24,
            ],
            280 => [
                'profile_id'  => 5,          // weekly solo
                'valid_hours' => 24 * 7,
            ],
            720 => [
                'profile_id'  => 6,          // monthly solo
                'valid_hours' => 24 * 30,
            ],
            4200 => [
                'profile_id'  => 7,          // qtrly family x4
                'valid_hours' => 24 * 90,
            ],
            default => [
                'profile_id'  => 2,
                'valid_hours' => 24,
            ],
        };
    }

    protected function handleSuccessfulPayment(Payment $payment): void
    {
        // Only proceed on successful payments, and only once
        if (! $payment->isSuccessful() || $payment->voucher_id) {
            return;
        }

        $plan = $this->mapPlanByAmount($payment);

        $voucher = Voucher::create([
            'router_id'          => $payment->router_id,
            'profile_id'         => $plan['profile_id'],
            'code'               => Str::upper(Str::random(8)),
            'face_value'         => $payment->amount,
            'status'             => 'new',
            'synced_to_mikrotik' => 0,
            'expires_at'         => now()->addHours($plan['valid_hours']),
            'customer_phone'     => $payment->phone,
            'hotspot_user_id'    => null,
        ]);

        $payment->voucher_id   = $voucher->id;
        $payment->voucher_code = $voucher->code;
        $payment->save();

        try {
            $admin = new TelegramAdmin();
            $admin->notify(new PaymentSucceeded($payment));
        } catch (\Throwable $e) {
            Log::error('Telegram notify failed', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
