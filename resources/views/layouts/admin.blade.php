<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Mawimbi Admin')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/[email protected]/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">Mawimbi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('vouchers.index') }}">Vouchers</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('payments.index') }}">Payments</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('plans.index') }}">Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('routers.index') }}">Routers</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/[email protected]/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
