@extends('layouts.app')

@section('content')
<h1>Admin Overview</h1>

<form method="GET" action="{{ route('admin.overview') }}" style="margin-bottom: 15px;">
    <label>Range:</label>
    <select name="range" onchange="this.form.submit()">
        <option value="today" {{ ($range ?? 'today') === 'today' ? 'selected' : '' }}>Today</option>
        <option value="7" {{ ($range ?? '') === '7' ? 'selected' : '' }}>Last 7 days</option>
        <option value="30" {{ ($range ?? '') === '30' ? 'selected' : '' }}>Last 30 days</option>
        <option value="all" {{ ($range ?? '') === 'all' ? 'selected' : '' }}>All time</option>
    </select>
    <noscript><button type="submit">Apply</button></noscript>
</form>

<h2>{{ $rangeLabel ?? 'Today' }} summary</h2>
<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>Payments (count)</th>
        <td>
            <a href="{{ route('payments.index', ['range' => $range ?? 'today']) }}">
                {{ $paymentsCount }}
            </a>
        </td>
    </tr>
    <tr>
        <th>Payments total (KES)</th>
        <td>{{ $paymentsTotal }}</td>
    </tr>
    <tr>
        <th>Vouchers created</th>
        <td>
            <a href="{{ route('vouchers.index', ['range' => $range ?? 'today']) }}">
                {{ $vouchersCreatedCount }}
            </a>
        </td>
    </tr>
    <tr>
        <th>Vouchers used</th>
        <td>
            <a href="{{ route('vouchers.index', ['status' => 'used', 'range' => $range ?? 'today']) }}">
                {{ $vouchersUsedCount }}
            </a>
        </td>
    </tr>
    <tr>
        <th>Expired but unused vouchers (as of now)</th>
        <td>{{ $expiredUnused }}</td>
    </tr>
</table>

@if(isset($profileStats) && $profileStats->count())
    <h2 style="margin-top:20px;">Per-profile stats ({{ $rangeLabel ?? 'Today' }})</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Created</th>
                <th>Used</th>
                <th>Estimated revenue (KES)</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($profileStats as $row)
            <tr>
                <td>{{ $row['profile']->name }}</td>
                <td>
                    <a href="{{ route('vouchers.index', ['range' => $range ?? 'today', 'profile_id' => $row['profile']->id]) }}">
                        {{ $row['created'] }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('vouchers.index', ['range' => $range ?? 'today', 'status' => 'used', 'profile_id' => $row['profile']->id]) }}">
                        {{ $row['used'] }}
                    </a>
                </td>
                <td>{{ number_format($row['estimated_revenue'], 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@endsection
