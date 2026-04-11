@extends('layouts.app')

@section('content')
    <h1>Create Payment</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payments.store') }}" method="POST">
        @csrf

        <div>
            <label for="type">Payment Type</label><br>
            <select name="type" id="type">
                <option value="mpesa" {{ old('type') === 'mpesa' ? 'selected' : '' }}>M-Pesa STK</option>
                <option value="manual" {{ old('type') === 'manual' ? 'selected' : '' }}>Manual (cash / M-Pesa)</option>
            </select>
        </div>

        <div style="margin-top: 10px;">
            <label for="phone">Customer Phone (MSISDN)</label><br>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="2547XXXXXXXX">
            <small>Optional for manual payments.</small>
        </div>

        <div style="margin-top: 10px;">
            <label for="amount">Amount</label><br>
            <input type="number" name="amount" id="amount" value="{{ old('amount', 50) }}" step="1" min="1">
        </div>

        <div style="margin-top: 10px;">
            <label for="router_id">Router</label><br>
            <select name="router_id" id="router_id">
                @foreach ($routers as $router)
                    <option value="{{ $router->id }}" @selected(old('router_id') == $router->id)>
                        {{ $router->name }} ({{ $router->ip_address }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-top: 10px;">
            <label for="voucher_code">Voucher Code (for manual payments)</label><br>
            <input type="text" name="voucher_code" id="voucher_code" value="{{ old('voucher_code') }}" placeholder="SR8AJEJU">
            <small>If set, this payment will be linked to that voucher.</small>
        </div>

        <div style="margin-top: 10px;">
            <label for="bundle">Bundle / Package</label><br>
            <input type="text" name="bundle" id="bundle" value="{{ old('bundle') }}" placeholder="Daily 50 / 1GB / etc">
        </div>

        <div style="margin-top: 15px;">
            <button type="submit">Save Payment</button>
            <a href="{{ route('payments.index') }}">Cancel</a>
        </div>
    </form>
@endsection
