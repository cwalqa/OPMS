<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class SessionExpiredMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    $excluded = [
        '/', 'login', 'logout',
        'admin-login', 'admin/login', 'admin/logout',
        'password/reset', 'password/email', 'password/reset/*',
        'quickbooks/*',
        '2fa', '2fa/resend',
        'admin/2fa', 'admin/2fa/resend',
    ];

    foreach ($excluded as $pattern) {
        if ($request->is($pattern)) {
            return $next($request);
        }
    }

    $isAdmin = Str::startsWith($request->path(), 'admin');
    $guard = $isAdmin ? 'admin' : 'web';

    if (!Auth::guard($guard)->check()) {
        return $request->expectsJson()
            ? response()->json(['session_expired' => true], 401)
            : redirect()->route($isAdmin ? 'admin.login.form' : 'login');
    }

    return $next($request);
}

}