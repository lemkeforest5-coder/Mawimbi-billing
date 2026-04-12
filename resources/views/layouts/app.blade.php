<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Mawimbi Billing') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f3f4f6;">

    <nav style="padding:8px 16px; border-bottom:1px solid #e5e7eb; font-size:14px; background:#111827; color:#e5e7eb;">
        <a href="{{ url('/') }}" style="color:#e5e7eb; text-decoration:none; margin-right:8px;">Status</a> |
        <a href="{{ url('/admin/overview') }}" style="color:#e5e7eb; text-decoration:none; margin:0 8px;">Admin overview</a> |
        <a href="{{ url('/routers') }}" style="color:#e5e7eb; text-decoration:none; margin:0 8px;">Routers</a> |
        <a href="{{ url('/profiles') }}" style="color:#e5e7eb; text-decoration:none; margin:0 8px;">Profiles</a> |
        <a href="{{ url('/vouchers') }}" style="color:#e5e7eb; text-decoration:none; margin:0 8px;">Vouchers</a> |
        <a href="{{ url('/payments') }}" style="color:#e5e7eb; text-decoration:none; margin-left:8px;">Payments</a>

        <span style="float:right;">
            @if(session('is_admin'))
                Admin
                <a href="{{ route('admin.logout') }}" style="color:#e5e7eb; margin-left:8px;">Logout</a>
            @else
                <a href="{{ route('admin.login') }}" style="color:#e5e7eb; margin-left:8px;">Login</a>
            @endif
        </span>
    </nav>

    <div style="padding:24px;">
        @yield('content')
    </div>

</body>
</html>
