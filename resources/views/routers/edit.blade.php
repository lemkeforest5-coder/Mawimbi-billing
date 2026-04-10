@extends('layouts.app')

@section('content')
<h1>Edit Router: {{ $router->name }}</h1>

@if ($errors->any())
    <div style="color: red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('routers.update', $router) }}" method="POST">
    @csrf
    @method('PUT')

    <p>
        <label>Name:</label><br>
        <input type="text" name="name" value="{{ old('name', $router->name) }}" required>
    </p>

    <p>
        <label>Location:</label><br>
        <input type="text" name="location" value="{{ old('location', $router->location) }}">
    </p>

    <p>
        <label>IP Address:</label><br>
        <input type="text" name="ip_address" value="{{ old('ip_address', $router->ip_address) }}" required>
    </p>

    <p>
        <label>API Port:</label><br>
        <input type="number" name="api_port" value="{{ old('api_port', $router->api_port) }}" required>
    </p>

    <p>
        <label>API Username:</label><br>
        <input type="text" name="api_username" value="{{ old('api_username', $router->api_username) }}" required>
    </p>

    <p>
        <label>API Password:</label><br>
        <input type="password" name="api_password" value="{{ old('api_password', $router->api_password) }}" required>
    </p>

    <p>
        <label>
            <input type="checkbox" name="enabled" value="1"
                {{ old('enabled', $router->enabled) ? 'checked' : '' }}>
            Enabled
        </label>
    </p>

    <p>
        <button type="submit">Update</button>
        <a href="{{ route('routers.index') }}">Cancel</a>
    </p>
</form>
@endsection
