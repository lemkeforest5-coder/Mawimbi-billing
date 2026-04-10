@extends('layouts.app')

@section('content')
<h1>Vouchers</h1>

@if (session('status'))
    <div style="color: green;">{{ session('status') }}</div>
@endif

<form method="GET" action="{{ route('vouchers.index') }}" style="margin-bottom: 15px;">
    <label>Search:</label>
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Code or phone">
    <label>Status:</label>
    <select name="status">
        <option value="">-- Any --</option>
        <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>Unused</option>
        <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
    </select>
    <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Profile</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Created</th>
            <th>Used at</th>
            <th>Expires at</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($vouchers as $voucher)
        <tr>
            <td>{{ $voucher->id }}</td>
            <td>{{ $voucher->code }}</td>
            <td>{{ $voucher->profile->name ?? '-' }}</td>
            <td>{{ $voucher->customer_phone ?? '-' }}</td>
            <td>{{ $voucher->status ?? '-' }}</td>
            <td>{{ $voucher->created_at }}</td>
            <td>{{ $voucher->used_at ?? '-' }}</td>
            <td>{{ $voucher->expires_at ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8">No vouchers found.</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div style="margin-top: 10px;">
    {{ $vouchers->links() }}
</div>
@endsection
