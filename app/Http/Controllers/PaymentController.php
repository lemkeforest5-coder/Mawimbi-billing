<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Router;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Payment::query();

        // For overall stats, only count successful payments
        $successful = (clone $baseQuery)->where('status', 'successful');

        $countAll    = (clone $successful)->count();
        $totalAmount = (clone $successful)->sum('amount');

        $totalToday = (clone $successful)
            ->whereDate('created_at', now()->toDateString())
            ->sum('amount');

        $total7 = (clone $successful)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->sum('amount');

        $total30 = (clone $successful)
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->sum('amount');

        // Per-router totals (successful only)
        $perRouter = (clone $successful)
            ->selectRaw('router_id, SUM(amount) as total_amount, COUNT(*) as cnt')
            ->groupBy('router_id')
            ->with('router')
            ->get();

        // List query with filters
        $query = Payment::with('router')->orderByDesc('created_at');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Provider filter
        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        // Exact voucher code filter (keep existing behaviour)
        if ($request->filled('voucher_code')) {
            $query->where('voucher_code', $request->voucher_code);
        }

        // Free-text search: phone, voucher_code, reference
        $search = trim($request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('voucher_code', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%");
            });
        }

        // Time filter: keep "last minutes" but add range presets
        $range = $request->input('range', ''); // '', today, 7, 30, all

        $from = $to = null;

        if ($range === 'today') {
            $from = now()->startOfDay();
            $to   = now()->endOfDay();
        } elseif ($range === '7') {
            $from = now()->startOfDay()->subDays(6);
            $to   = now()->endOfDay();
        } elseif ($range === '30') {
            $from = now()->startOfDay()->subDays(29);
            $to   = now()->endOfDay();
        } elseif ($range === 'all' || $range === '') {
            // no date constraint from range
        }

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        if ($request->filled('minutes')) {
            $query->where('created_at', '>=', now()->subMinutes((int) $request->minutes));
        }

        $payments = $query->get();

        return view('payments.index', compact(
            'payments',
            'countAll',
            'totalAmount',
            'totalToday',
            'total7',
            'total30',
            'perRouter'
        ) + [
            'search' => $search,
            'range'  => $range,
        ]);
    }

    public function voucher(Payment $payment)
    {
        if (! $payment->voucher_id) {
            return response()->json([
                'ok'      => false,
                'message' => 'Voucher not ready yet.',
            ]);
        }

        $voucher = $payment->voucher;

        if (! $voucher) {
            return response()->json([
                'ok'      => false,
                'message' => 'Voucher not found.',
            ]);
        }

        return response()->json([
            'ok'           => true,
            'code'         => $voucher->code,
            'profile'      => optional($voucher->profile)->name,
            'time_seconds' => $voucher->time_limit_seconds,
        ]);
    }

    public function show(Payment $payment)
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
            'type'        => ['required', 'in:mpesa,manual'],
            'phone'       => ['nullable', 'string', 'min:10'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'router_id'   => ['required', 'exists:routers,id'],
            'bundle'      => ['nullable', 'string', 'max:191'],
            'voucher_code'=> ['nullable', 'string', 'max:64'],
        ]);

        if ($validated['type'] === 'manual') {
            $voucher = null;

            if (! empty($validated['voucher_code'])) {
                $voucher = Voucher::where('code', $validated['voucher_code'])->first();
            }

            $payment = Payment::create([
                'router_id'     => $validated['router_id'],
                'voucher_id'    => $voucher?->id,
                'provider'      => 'manual',
                'reference'     => 'MANUAL-' . time() . '-' . rand(1000, 9999),
                'voucher_code'  => $validated['voucher_code'] ?? null,
                'phone'         => $validated['phone'] ?? null,
                'amount'        => $validated['amount'],
                'status'        => 'successful',
                'result_description' => 'Manual payment recorded',
                'payload'       => null,
            ]);

            return redirect()
                ->route('payments.index')
                ->with('success', 'Manual payment saved' . ($voucher ? ' and linked to voucher '.$voucher->code : ''));
        }

        // M-Pesa STK push flow
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
