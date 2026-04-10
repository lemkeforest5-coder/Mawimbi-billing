@extends('layouts.app')

@section('content')
<h1>Vouchers</h1>

@if(session('status'))
    <div style="color: green;">{{ session('status') }}</div>
@endif

<p>
    <a href="{{ route('vouchers.create') }}">Create New Voucher</a>
</p>

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
    <tr>
        <th>ID</th>
        <th>Code</th>
        <th>Router</th>
        <th>Profile</th>
        <th>Status</th>
        <th>Synced</th>
        <th>Expires</th>
        <th>Used At</th>
        <th>Created At</th>
        <th></th>
    </tr>
</thead>
    <tbody>
    @forelse($vouchers as $v)
        <tr>
            <td>{{ $v->id }}</td>
            <td>{{ $v->code }}</td>
            <td>{{ $v->router?->name }}</td>
            <td>{{ $v->profile?->name }}</td>
            <td>{{ $v->status }}</td>
            <td>{{ $v->synced_to_mikrotik ? 'Yes' : 'No' }}</td>
            <td>{{ optional($v->expires_at)->toDateTimeString() }}</td>
            <td>{{ optional($v->used_at)->toDateTimeString() ?: '-' }}</td>
            <td>{{ $v->created_at->toDateTimeString() }}</td>
            <td>
                <a href="{{ route('vouchers.edit', $v) }}">Edit</a>

                <form action="{{ route('vouchers.destroy', $v) }}" method="post" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this voucher?')">Delete</button>
                </form>

                <form action="{{ route('vouchers.sendToMikrotik', $v) }}" method="post" style="display:inline;">
                    @csrf
                    <button type="submit">Send to Mikrotik</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="9">No vouchers yet.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top:10px;">
    {{ $vouchers->links() }}
</div>
@endsection
