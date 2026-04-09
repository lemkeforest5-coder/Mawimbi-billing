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
            <label for="phone">Customer Phone (MSISDN)</label><br>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="2547XXXXXXXX">
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
            <label for="bundle">Bundle / Package</label><br>
            <input type="text" name="bundle" id="bundle" value="{{ old('bundle') }}" placeholder="Daily 50 / 1GB / etc">
        </div>

        <div style="margin-top: 15px;">
            <button type="submit">Initiate Payment</button>
            <a href="{{ route('payments.index') }}">Cancel</a>
        </div>
    </form>
@endsection
