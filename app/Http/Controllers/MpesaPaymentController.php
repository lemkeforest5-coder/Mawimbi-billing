<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\MpesaService;

class MpesaPaymentController extends Controller
{
    protected function resolveAmount(Request $request): int
    {
        $amount = (int) $request->input('amount');

        $allowed = [10, 20, 80, 280, 720, 4200];

        if (! in_array($amount, $allowed, true)) {
            abort(400, 'Invalid amount');
        }

        return $amount;
    }

    public function initiate(Request $request, MpesaService $mpesa)
    {
        $request->validate([
            'phone'       => 'required',
            'amount'      => 'required|numeric',
            'plan_key'    => 'nullable|string',
            'mac_address' => 'nullable|string',
            'ip_address'  => 'nullable|string',
            'router_id'   => 'required|integer',
        ]);

        $amount = $this->resolveAmount($request);

        $phone    = $request->input('phone');
        $routerId = (int) $request->input('router_id');

        $reference = 'HS-' . now()->format('YmdHis') . '-' . rand(1000, 9999);

        $payment = Payment::create([
            'router_id' => $routerId,
            'voucher_id'=> null,
            'provider'  => 'mpesa',
            'reference' => $reference,
            'phone'     => $phone,
            'amount'    => $amount,
            'status'    => 'pending',
            'payload'   => null,
            'description' => $request->input('plan_key'),
        ]);

        Log::info('Initiating STK for payment', [
            'payment_id' => $payment->id,
            'phone'      => $phone,
            'amount'     => $amount,
            'router_id'  => $routerId,
        ]);

        try {
            $response = $mpesa->stkPush([
                'phone'     => $payment->phone,
                'amount'    => $payment->amount,
                'reference' => $payment->reference,
            ]);

            $payment->update([
                'payload' => $response,
            ]);

            $msg = $response['ResponseDescription'] ?? 'STK push sent.';
        } catch (\Throwable $e) {
            Log::error('STK push error (hotspot)', ['error' => $e->getMessage()]);
            return response()->json([
                'ok'      => false,
                'message' => 'Failed to initiate M-Pesa STK push.',
            ], 500);
        }

        return response()->json([
            'ok'         => true,
            'message'    => $msg,
            'payment_id' => $payment->id,
        ]);
    }
}
