@extends('layouts.app')

@section('content')
    <h1>Vouchers</h1>

    <form method="GET" action="{{ url('/vouchers') }}">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Code or phone">

        <select name="status">
            <option value="">All statuses</option>
            <option value="new"   {{ request('status') === 'new' ? 'selected' : '' }}>Unused</option>
            <option value="used"  {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>

        <button type="submit">Filter</button>
    </form>

    <table border="1" cellpadding="5" cellspacing="0">
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
            <th>Total Time</th>
            <th>Total Data (MB)</th>
            <th>Remaining Time</th>
            <th>Remaining Data (MB)</th>
        </tr>
        </thead>
        <tbody>
        @forelse($vouchers as $voucher)
            <tr>
                <td>{{ $voucher->id }}</td>
                <td>{{ $voucher->code }}</td>
                <td>{{ $voucher->profile->name ?? '-' }}</td>
                <td>{{ $voucher->customer_phone ?? '-' }}</td>
                <td>{{ $voucher->status }}</td>
                <td>{{ $voucher->created_at }}</td>
                <td>{{ $voucher->used_at ?? '-' }}</td>
                <td>{{ $voucher->expires_at ?? '-' }}</td>
                <td>{{ $voucher->total_time_human }}</td>
                <td>{{ number_format($voucher->total_data_mb) }}</td>
                <td>
                    @if (! is_null($voucher->remaining_time_seconds))
                        @php
                            $rem = $voucher->remaining_time_seconds;
                            $h = intdiv($rem, 3600);
                            $m = intdiv($rem % 3600, 60);
                        @endphp
                        {{ sprintf('%02dh %02dm', $h, $m) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if (! is_null($voucher->remaining_data_mb))
                        {{ number_format($voucher->remaining_data_mb) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12">No vouchers found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    @if(method_exists($vouchers, 'links'))
        <div>
            {{ $vouchers->links() }}
        </div>
    @endif
@endsection
