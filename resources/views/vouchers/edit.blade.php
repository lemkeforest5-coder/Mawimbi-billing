@extends('layouts.app')

@section('content')
<h1>Edit Voucher</h1>

@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('vouchers.update', $voucher) }}">
    @csrf
    @method('PUT')

    <label>Router:
        <select name="router_id">
            @foreach($routers as $router)
                <option value="{{ $router->id }}" @selected(old('router_id', $voucher->router_id) == $router->id)>
                    {{ $router->name }} ({{ $router->ip_address }})
                </option>
            @endforeach
        </select>
    </label><br><br>

    <label>Profile:
        <select name="profile_id">
            @foreach($profiles as $profile)
                <option value="{{ $profile->id }}" @selected(old('profile_id', $voucher->profile_id) == $profile->id)>
                    {{ $profile->name }}
                </option>
            @endforeach
        </select>
    </label><br><br>

    <label>Code:
        <input type="text" name="code" value="{{ old('code', $voucher->code) }}">
    </label><br><br>

    <label>Status:
        <input type="text" name="status" value="{{ old('status', $voucher->status) }}">
    </label><br><br>

    <label>Expires At:
        <input type="datetime-local" name="expires_at"
               value="{{ old('expires_at', optional($voucher->expires_at)->format('Y-m-d\TH:i')) }}">
    </label><br><br>

    <button type="submit">Update</button>
</form>
@endsection
