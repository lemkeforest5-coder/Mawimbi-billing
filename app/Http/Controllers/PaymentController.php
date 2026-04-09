<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Router;
use Illuminate\Http\Request;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
   public function index(Request $request)
{
    $query = Payment::with('router')->orderByDesc('created_at');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('minutes')) {
        $query->where('created_at', '>=', now()->subMinutes((int) $request->minutes));
    }

    $payments = $query->get();

    return view('payments.index', compact('payments'));
}
public function show(\App\Models\Payment $payment)
{
    return view('payments.show', compact('payment'));
}

    public function create()
    {
        $routers = Router::orderBy('name')->get();

        return view('payments.create', compact('routers'));
    }

    public function store(Request $request, MpesaService $mpesa)
    {
        $validated = $request->validate([
            'phone'     => ['required', 'string', 'min:10'],
            'amount'    => ['required', 'numeric', 'min:1'],
            'router_id' => ['required', 'exists:routers,id'],
            'bundle'    => ['nullable', 'string', 'max:191'], // stays only in form, not DB
        ]);

        $reference = 'PENDING-' . time() . '-' . rand(1000, 9999);

        $payment = Payment::create([
            'router_id' => $validated['router_id'],
            'voucher_id'=> null,
            'provider'  => 'mpesa',
            'reference' => $reference,
            'phone'     => $validated['phone'],
            'amount'    => $validated['amount'],
            'status'    => 'pending',
            'payload'   => null,
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

            $msg = $response['ResponseDescription'] ?? 'STK push sent (sandbox).';
        } catch (\Throwable $e) {
            Log::error('STK push error', ['error' => $e->getMessage()]);
            $msg = 'Failed to initiate M-Pesa STK push (sandbox).';
        }

        return redirect()
            ->route('payments.index')
            ->with('success', $msg);
    }
}
