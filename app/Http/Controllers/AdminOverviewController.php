<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Payment;
use Carbon\Carbon;

class AdminOverviewController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayPaymentsCount = Payment::whereDate('created_at', $today)->count();
        $todayPaymentsTotal = Payment::whereDate('created_at', $today)->sum('amount');

        $todayVouchersCreated = Voucher::whereDate('created_at', $today)->count();
        $todayVouchersUsed    = Voucher::whereDate('used_at', $today)->count();

        $expiredUnused = Voucher::where('status', 'unused')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        return view('admin.overview', compact(
            'todayPaymentsCount',
            'todayPaymentsTotal',
            'todayVouchersCreated',
            'todayVouchersUsed',
            'expiredUnused'
        ));
    }
}
