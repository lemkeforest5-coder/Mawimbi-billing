@extends('layouts.app')

@section('content')
    <h1>Batch print vouchers</h1>

    @if (session('status'))
        <p style="color: green;">{{ session('status') }}</p>
    @endif

    <p>Showing the 60 most recent vouchers. Select the ones you want to print on a single page.</p>

    <form method="POST" action="{{ route('vouchers.batch.print') }}" target="_blank">
        @csrf

        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
            <tr>
                <th><input type="checkbox" onclick="toggleAll(this)"></th>
                <th>ID</th>
                <th>Code</th>
                <th>Profile</th>
                <th>Status</th>
                <th>Created at</th>
            </tr>
            </thead>
            <tbody>
            @forelse($vouchers as $voucher)
                <tr>
                    <td>
                        <input type="checkbox" name="voucher_ids[]" value="{{ $voucher->id }}">
                    </td>
                    <td>{{ $voucher->id }}</td>
                    <td>{{ $voucher->code }}</td>
                    <td>{{ $voucher->profile->name ?? '-' }}</td>
                    <td>{{ $voucher->status }}</td>
                    <td>{{ $voucher->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No vouchers found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <p style="margin-top: 10px;">
            <button type="submit">Print selected vouchers</button>
        </p>
    </form>

    <script>
        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('input[name="voucher_ids[]"]');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }
    </script>
@endsection
