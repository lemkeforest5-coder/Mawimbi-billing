@extends('layouts.app')

@section('content')
    <h1>Voucher #{{ $voucher->id }} ({{ $voucher->code }})</h1>

    <p>
        <a href="{{ route('vouchers.index') }}">Back to list</a> |
        <a href="{{ route('vouchers.print', $voucher) }}" target="_blank">Print this voucher</a>
    </p>

    <h2>Details</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>Code</th><td>{{ $voucher->code }}</td></tr>
        <tr><th>Status</th><td>{{ $voucher->status }}</td></tr>
        <tr><th>Profile</th><td>{{ $voucher->profile->name ?? '-' }}</td></tr>
        <tr><th>Router</th><td>{{ $voucher->router->name ?? '-' }}</td></tr>
        <tr><th>Customer phone</th><td>{{ $voucher->customer_phone ?? '-' }}</td></tr>
        <tr><th>Created at</th><td>{{ $voucher->created_at }}</td></tr>
        <tr><th>Used at</th><td>{{ $voucher->used_at ?? '-' }}</td></tr>
        <tr><th>Expires at</th><td>{{ $voucher->expires_at ?? '-' }}</td></tr>
        <tr><th>Total time</th><td>{{ $voucher->total_time_human }}</td></tr>
        <tr><th>Total data (MB)</th><td>{{ number_format($voucher->total_data_mb ?? 0) }}</td></tr>
        <tr>
            <th>Remaining time</th>
            <td>
                @if (! is_null($voucher->remaining_time_seconds))
                    @php
                        $rem = $voucher->remaining_time_seconds;
                        $h = intdiv($rem, 3600);
                        $m = intdiv($rem % 3600, 60);
                    @endphp
                    {{ sprintf('%02dh %02dm', $h, $m) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>Remaining data (MB)</th>
            <td>
                @if (! is_null($voucher->remaining_data_mb))
                    {{ number_format($voucher->remaining_data_mb) }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <h2 style="margin-top: 20px;">Payments</h2>
    @if ($voucher->payments->isEmpty())
        <p>No payments recorded for this voucher.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Reference</th>
                <th>Created at</th>
            </tr>
            </thead>
            <tbody>
            @foreach($voucher->payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->method ?? '-' }}</td>
                    <td>{{ $payment->reference ?? '-' }}</td>
                    <td>{{ $payment->created_at }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection
