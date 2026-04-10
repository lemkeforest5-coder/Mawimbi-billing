@extends('layouts.app')

@section('content')
    <h1>Profiles</h1>

    @if (session('status'))
        <div style="color: green;">{{ session('status') }}</div>
    @endif

    <p>
        <a href="{{ route('profiles.create') }}">Create new profile</a>
    </p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Router</th>
                <th>Name</th>
                <th>Code</th>
                <th>Rate limit</th>
                <th>Time (minutes)</th>
                <th>Data (MB)</th>
                <th>Price (KES)</th>
                <th>Default</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($profiles as $profile)
            <tr>
                <td>{{ $profile->id }}</td>
                <td>{{ $profile->router->name ?? '-' }}</td>
                <td>{{ $profile->name }}</td>
                <td>{{ $profile->code }}</td>
                <td>{{ $profile->rate_limit ?? '-' }}</td>
                <td>{{ $profile->time_limit_minutes ?? '-' }}</td>
                <td>{{ $profile->data_limit_mb ?? '-' }}</td>
                <td>{{ $profile->price }}</td>
                <td>{{ $profile->is_default ? 'Yes' : 'No' }}</td>
                <td>
                    <a href="{{ route('profiles.edit', $profile) }}">Edit</a>

                    <form action="{{ route('profiles.destroy', $profile) }}" method="POST" style="display:inline"
                          onsubmit="return confirm('Delete this profile?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="10">No profiles found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 10px;">
        {{ $profiles->links() }}
    </div>
@endsection
