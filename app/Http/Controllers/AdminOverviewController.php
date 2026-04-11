<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Payment;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminOverviewController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->input('range', 'today'); // today, 7, 30, all

        switch ($range) {
            case '7':
                $from  = Carbon::today()->subDays(6); // last 7 days incl. today
                $to    = Carbon::today();
                $label = 'Last 7 days';
                break;
            case '30':
                $from  = Carbon::today()->subDays(29);
                $to    = Carbon::today();
                $label = 'Last 30 days';
                break;
            case 'all':
                $from  = null;
                $to    = null;
                $label = 'All time';
                break;
            case 'today':
            default:
                $from  = Carbon::today();
                $to    = Carbon::today();
                $label = 'Today';
                $range = 'today';
                break;
        }

        // Payments
        $paymentsQuery = Payment::query();
        if ($from && $to) {
            $paymentsQuery->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        }
        $payments      = $paymentsQuery->get();
        $paymentsCount = $payments->count();
        $paymentsTotal = $payments->sum('amount');

        // Payment status and provider breakdown
        $paymentsSuccessful  = $payments->where('status', 'Successful')->count();
        $paymentsPending     = $payments->where('status', 'Pending')->count();
        $paymentsFailed      = $payments->where('status', 'Failed')->count();
        $paymentsMpesaTotal  = $payments->where('provider', 'mpesa')->sum('amount');
        $paymentsManualTotal = $payments->where('provider', 'manual')->sum('amount');

        // Vouchers created
        $vouchersCreatedQuery = Voucher::query();
        if ($from && $to) {
            $vouchersCreatedQuery->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        }
        $vouchersCreatedCount = $vouchersCreatedQuery->count();

        // Vouchers used
        $vouchersUsedQuery = Voucher::query();
        if ($from && $to) {
            $vouchersUsedQuery->whereBetween('used_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
        }
        $vouchersUsedCount = $vouchersUsedQuery->count();

        // Expired but unused – always "now"-based
        $expiredUnused = Voucher::where('status', 'unused')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->count();

        // Per-profile stats for selected range
        $profiles = Profile::orderBy('name')->get();

        $profileStats = $profiles->map(function (Profile $profile) use ($from, $to) {
            $createdQuery = Voucher::where('profile_id', $profile->id);
            $usedQuery    = Voucher::where('profile_id', $profile->id);

            if ($from && $to) {
                $createdQuery->whereBetween('created_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
                $usedQuery->whereBetween('used_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
            }

            $createdCount     = $createdQuery->count();
            $usedCount        = $usedQuery->count();
            $estimatedRevenue = $usedCount * ($profile->price ?? 0);

            return [
                'profile'           => $profile,
                'created'           => $createdCount,
                'used'              => $usedCount,
                'estimated_revenue' => $estimatedRevenue,
            ];
        });

        // Top 3 profiles by estimated revenue for this range
        $topProfiles = $profileStats
            ->sortByDesc('estimated_revenue')
            ->take(3)
            ->values();

        return view('admin.overview', [
            'range'                => $range,
            'rangeLabel'           => $label,
            'paymentsCount'        => $paymentsCount,
            'paymentsTotal'        => $paymentsTotal,
            'paymentsSuccessful'   => $paymentsSuccessful,
            'paymentsPending'      => $paymentsPending,
            'paymentsFailed'       => $paymentsFailed,
            'paymentsMpesaTotal'   => $paymentsMpesaTotal,
            'paymentsManualTotal'  => $paymentsManualTotal,
            'vouchersCreatedCount' => $vouchersCreatedCount,
            'vouchersUsedCount'    => $vouchersUsedCount,
            'expiredUnused'        => $expiredUnused,
            'profileStats'         => $profileStats,
            'topProfiles'          => $topProfiles,
        ]);
    }
}
