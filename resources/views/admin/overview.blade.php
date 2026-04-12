@extends('layouts.app')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto; background:#ffffff; border-radius:8px; padding:20px 24px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
            <h1 style="font-size:22px; font-weight:600; margin:0;">Admin Overview</h1>

            <button
                type="button"
                data-theme-toggle
                style="padding:4px 10px; border-radius:9999px; border:1px solid #64748b; background:#0f172a; color:#e5e7eb; font-size:12px; cursor:pointer;"
            >
                Toggle theme
            </button>
        </div>

        <form method="GET" action="{{ route('admin.overview') }}" style="margin-bottom:15px;">
            <label>Range:
                <select name="range" onchange="this.form.submit()">
                    <option value="today" {{ ($range ?? 'today') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="7" {{ ($range ?? '') === '7' ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ ($range ?? '') === '30' ? 'selected' : '' }}>Last 30 days</option>
                    <option value="all" {{ ($range ?? '') === 'all' ? 'selected' : '' }}>All time</option>
                </select>
            </label>
            <noscript><button type="submit">Apply</button></noscript>
        </form>

        <style>
            .admin-table { border-collapse:collapse; font-size:14px; margin-bottom:18px; }
            .admin-table th,
            .admin-table td { border:1px solid #e5e7eb; padding:4px 8px; }
            .admin-table th { background:#f9fafb; text-align:left; }
        </style>

        <h2 style="margin-top:10px; margin-bottom:6px;">{{ $rangeLabel ?? 'Today' }}</h2>
        <table class="admin-table">
            <tr>
                <th>Total payments</th>
                <td>{{ $paymentsCount }}</td>
            </tr>
            <tr>
                <th>Total revenue</th>
                <td>Ksh {{ number_format($paymentsTotal, 2) }}</td>
            </tr>
            <tr>
                <th>Vouchers created</th>
                <td>{{ $vouchersCreatedCount }}</td>
            </tr>
            <tr>
                <th>Vouchers used</th>
                <td>{{ $vouchersUsedCount }}</td>
            </tr>
            <tr>
                <th>Expired (unused) vouchers</th>
                <td>{{ $expiredUnused }}</td>
            </tr>
        </table>

        <h2 style="margin-top:18px; margin-bottom:6px;">Payments ({{ $rangeLabel ?? 'Today' }})</h2>
        <table class="admin-table">
            <tr>
                <th>Successful payments</th>
                <td>{{ $paymentsSuccessful }}</td>
            </tr>
            <tr>
                <th>Pending payments</th>
                <td>{{ $paymentsPending }}</td>
            </tr>
            <tr>
                <th>Failed payments</th>
                <td>{{ $paymentsFailed }}</td>
            </tr>
            <tr>
                <th>M-Pesa amount</th>
                <td>Ksh {{ number_format($paymentsMpesaTotal, 2) }}</td>
            </tr>
            <tr>
                <th>Manual amount</th>
                <td>Ksh {{ number_format($paymentsManualTotal, 2) }}</td>
            </tr>
        </table>

        <h2 style="margin-top:18px; margin-bottom:6px;">Per Profile</h2>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Profile</th>
                <th>Created</th>
                <th>Used</th>
                <th>Estimated revenue (used × price)</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($profileStats as $row)
                <tr>
                    <td>{{ $row['profile']->name ?? 'N/A' }}</td>
                    <td>{{ $row['created'] }}</td>
                    <td>{{ $row['used'] }}</td>
                    <td>Ksh {{ number_format($row['estimated_revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No profiles found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <h2 style="margin-top:18px; margin-bottom:6px;">Top profiles ({{ $rangeLabel ?? 'Today' }})</h2>
        <table class="admin-table">
            <thead>
            <tr>
                <th>Profile</th>
                <th>Vouchers used</th>
                <th>Estimated revenue</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($topProfiles as $row)
                <tr>
                    <td>{{ $row['profile']->name ?? 'N/A' }}</td>
                    <td>{{ $row['used'] }}</td>
                    <td>Ksh {{ number_format($row['estimated_revenue'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No top profiles yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
