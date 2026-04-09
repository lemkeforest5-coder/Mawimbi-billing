@extends('layouts.app')

@section('content')
<h1>Routers</h1>

@if(session('status'))
    <div style="color: green;">{{ session('status') }}</div>
@endif

<p><a href="{{ route('routers.create') }}">+ New Router</a></p>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>IP</th>
            <th>API Port</th>
            <th>Location</th>
            <th>Enabled</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($routers as $router)
        <tr>
            <td>{{ $router->name }}</td>
            <td>{{ $router->ip_address }}</td>
            <td>{{ $router->api_port }}</td>
            <td>{{ $router->location }}</td>
            <td>{{ $router->enabled ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('routers.edit', $router) }}">Edit</a>
            </td>
        </tr>
    @empty
        <tr><td colspan="6">No routers yet.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
