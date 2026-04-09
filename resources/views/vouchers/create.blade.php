@extends('layouts.app')

@section('content')
<h1>Create Voucher</h1>

@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('vouchers.store') }}">
    @csrf

    <label>Router:
        <select name="router_id">
            <option value="">-- select --</option>
            @foreach($routers as $router)
                <option value="{{ $router->id }}" @selected(old('router_id') == $router->id)>
                    {{ $router->name }} ({{ $router->ip_address }})
                </option>
            @endforeach
        </select>
    </label><br><br>

    <label>Profile:
        <select name="profile_id">
            <option value="">-- select --</option>
            @foreach($profiles as $profile)
                <option value="{{ $profile->id }}" @selected(old('profile_id') == $profile->id)>
                    {{ $profile->name }}
                </option>
            @endforeach
        </select>
    </label><br><br>

    <label>Code (leave blank to auto-generate):
        <input type="text" name="code" value="{{ old('code') }}">
    </label><br><br>

    <label>Expires At (optional):
        <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}">
    </label><br><br>

    <button type="submit">Save</button>
</form>
@endsection
