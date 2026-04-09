@extends('layouts.app')

@section('content')
<h1>Create Router</h1>

@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('routers.store') }}">
    @csrf

    <label>Name:
        <input type="text" name="name" value="{{ old('name') }}">
    </label><br><br>

    <label>Location:
        <input type="text" name="location" value="{{ old('location') }}">
    </label><br><br>

    <label>IP Address:
        <input type="text" name="ip_address" value="{{ old('ip_address') }}">
    </label><br><br>

    <label>API Port:
        <input type="number" name="api_port" value="{{ old('api_port', 8728) }}">
    </label><br><br>

    <label>API Username:
        <input type="text" name="api_username" value="{{ old('api_username') }}">
    </label><br><br>

    <label>API Password:
        <input type="text" name="api_password" value="{{ old('api_password') }}">
    </label><br><br>

    <label>
        <input type="checkbox" name="enabled" {{ old('enabled', true) ? 'checked' : '' }}>
        Enabled
    </label><br><br>

    <button type="submit">Save</button>
</form>
@endsection
