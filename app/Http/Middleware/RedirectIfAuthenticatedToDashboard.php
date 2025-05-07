<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToDashboard
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('client.dashboard');
        }

        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
