@extends('layouts.app')

@section('content')
<h1>Profiles</h1>

@if(session('status'))
    <div style="color: green;">{{ session('status') }}</div>
@endif

<p><a href="{{ route('profiles.create') }}">+ New Profile</a></p>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Router</th>
            <th>Rate limit</th>
            <th>Time (min)</th>
            <th>Data (MB)</th>
            <th>Price</th>
            <th>Default</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($profiles as $profile)
        <tr>
            <td>{{ $profile->name }}</td>
            <td>{{ $profile->code }}</td>
            <td>{{ optional($profile->router)->name }}</td>
            <td>{{ $profile->rate_limit }}</td>
            <td>{{ $profile->time_limit_minutes }}</td>
            <td>{{ $profile->data_limit_mb }}</td>
            <td>{{ $profile->price }}</td>
            <td>{{ $profile->is_default ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('profiles.edit', $profile) }}">Edit</a>
                <form action="{{ route('profiles.destroy', $profile) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this profile?')">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="9">No profiles yet.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $profiles->links() }}
@endsection
