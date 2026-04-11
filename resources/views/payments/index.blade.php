@extends('layouts.app')

@section('content')
    <h1>Payments</h1>

    <p>
        <a href="{{ route('payments.create') }}">Create New Payment</a>
    </p>

    @isset($totalAmount)
        <div style="margin-bottom: 10px; padding: 8px; border: 1px solid #ccc;">
            <strong>Summary (successful payments)</strong><br>
            Total payments: {{ $countAll }}<br>
            Total amount (all time): {{ number_format($totalAmount, 2) }}<br>
            Today: {{ number_format($totalToday, 2) }}<br>
            Last 7 days: {{ number_format($total7, 2) }}<br>
            Last 30 days: {{ number_format($total30, 2) }}
        </div>

        @if(isset($perRouter) && $perRouter->count())
            <div style="margin-bottom: 10px; padding: 8px; border: 1px solid #ccc;">
                <strong>Per-router revenue (successful payments)</strong>
                <table border="1" cellpadding="3" cellspacing="0" style="margin-top:5px; width:100%;">
                    <thead>
                        <tr>
                            <th>Router</th>
                            <th>Payments count</th>
                            <th>Total amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($perRouter as $row)
                            <tr>
                                <td>{{ optional($row->router)->name ?? ('Router #'.$row->router_id) }}</td>
                                <td>{{ $row->cnt }}</td>
                                <td>{{ number_format($row->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endisset

    <form method="GET" action="{{ route('payments.index') }}" style="margin-bottom: 10px;">
        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="">All</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="successful" {{ request('status') === 'successful' ? 'selected' : '' }}>Successful</option>
            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>

        <label for="provider">Provider:</label>
        <select name="provider" id="provider">
            <option value="">All</option>
            <option value="mpesa" {{ request('provider') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
            <option value="manual" {{ request('provider') === 'manual' ? 'selected' : '' }}>Manual</option>
        </select>

        <label for="voucher_code">Voucher code:</label>
        <input type="text" name="voucher_code" id="voucher_code" value="{{ request('voucher_code') }}" style="width: 120px;">

        <label for="minutes">Last minutes:</label>
        <input type="number" name="minutes" id="minutes" value="{{ request('minutes') }}" style="width: 80px;">

        <button type="submit">Filter</button>
    </form>

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if ($payments->isEmpty())
        <p>No payments found.</p>
    @else
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Reference</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Router</th>
                    <th>Provider</th>
                    <th>Status</th>
                    <th>Result Desc</th>
                    <th>Voucher</th>
                    <th>Created At</th>
                    <th>When</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->reference }}</td>
                        <td>{{ $payment->phone }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->router->name ?? '-' }}</td>
                        <td>{{ $payment->provider }}</td>
                        <td>
                            @if ($payment->status === 'pending')
                                Pending
                            @elseif ($payment->status === 'successful')
                                Successful
                            @else
                                Failed
                            @endif
                        </td>
                        <td>
                            @php
                                $payload = is_array($payment->payload)
                                    ? $payment->payload
                                    : json_decode($payment->payload ?? '', true);
                                $desc = $payload['Body']['stkCallback']['ResultDesc'] ?? $payment->result_description ?? null;
                            @endphp
                            {{ $desc ?? '-' }}
                        </td>
                        <td>{{ $payment->voucher_code ?? '-' }}</td>
                        <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ $payment->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
