@extends('layouts.app')

@section('content')
<h1>Admin Overview</h1>

<table border="1" cellpadding="6" cellspacing="0">
    <tr>
        <th>Today payments (count)</th>
        <td>{{ $todayPaymentsCount }}</td>
    </tr>
    <tr>
        <th>Today payments total (KES)</th>
        <td>{{ $todayPaymentsTotal }}</td>
    </tr>
    <tr>
        <th>Vouchers created today</th>
        <td>{{ $todayVouchersCreated }}</td>
    </tr>
    <tr>
        <th>Vouchers used today</th>
        <td>{{ $todayVouchersUsed }}</td>
    </tr>
    <tr>
        <th>Expired but unused vouchers</th>
        <td>{{ $expiredUnused }}</td>
    </tr>
</table>
@endsection
