<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mawimbi Billing</title>
</head>
<body>
    <nav>
        <a href="{{ url('/') }}">Status</a> |
        <a href="{{ url('/routers') }}">Routers</a> |
        <a href="{{ url('/profiles') }}">Profiles</a> |
        <a href="{{ url('/vouchers') }}">Vouchers</a> |
        <a href="{{ url('/payments') }}">Payments</a>
    </nav>
    <hr>
    @yield('content')
</body>
</html>
