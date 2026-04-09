@extends('layouts.app')

@section('content')
<h1>Payment {{ $payment->reference }}</h1>

<p>Status: {{ $payment->status }}</p>
<p>Amount: {{ $payment->amount }}</p>
<p>Phone: {{ $payment->phone }}</p>
<p>Router: {{ optional($payment->router)->name }}</p>

@if($payment->voucher_code)
    <h2>Your Voucher Code</h2>
    <p style="font-size: 2rem; font-weight: bold;">
        {{ $payment->voucher_code }}
    </p>

    <h3>How to connect</h3>
    <ol>
        <li>Connect to Wi‑Fi network: <strong>YOUR-SSID-NAME</strong>.</li>
        <li>When the login page opens, enter the voucher code as both username and password.</li>
        <li>Click Login and start browsing.</li>
    </ol>
@else
    <p>Voucher not yet generated. Please wait a few seconds then refresh this page.</p>
@endif
@endsection
