<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminGate
{
    public function handle(Request $request, Closure $next): Response
    {
        // Publicly allow health and root JSON
        if ($request->is('/') || $request->is('up')) {
            return $next($request);
        }

        // If already "logged in" as admin, allow
        if ($request->session()->get('is_admin', false) === true) {
            return $next($request);
        }

        // If POSTing password, validate
        if ($request->isMethod('post') && $request->path() === 'admin/login') {
            $password = $request->input('password');
            $expected = env('ADMIN_PASSWORD');

            if ($expected && hash_equals($expected, (string) $password)) {
                $request->session()->put('is_admin', true);

                $intended = $request->session()->pull('admin_intended', url('/vouchers'));

                return redirect()->to($intended);
            }

            return response($this->loginForm('Invalid password.'), 401);
        }

        // If hitting login form, just show it
        if ($request->path() === 'admin/login') {
            return response($this->loginForm(), 200);
        }

        // Store intended URL and redirect to login
        $request->session()->put('admin_intended', $request->fullUrl());

        return redirect('/admin/login');
    }

    protected function loginForm(string $error = ''): string
    {
        $errorHtml = $error ? '<p style="color:red;">'.$error.'</p>' : '';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mawimbi Admin Login</title>
</head>
<body>
    <h1>Mawimbi Admin Login</h1>
    {$errorHtml}
    <form method="POST" action="/admin/login">
        <input type="hidden" name="_token" value="{$this->csrfToken()}">
        <label>Password:
            <input type="password" name="password" autofocus>
        </label>
        <button type="submit">Login</button>
    </form>
</body>
</html>
HTML;
    }

    protected function csrfToken(): string
    {
        return csrf_token();
    }
}
