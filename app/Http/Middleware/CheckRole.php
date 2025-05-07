<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        // Check if the authenticated admin has the required role
        if (!Auth::guard('admin')->check() || !Auth::guard('admin')->user()->hasRole($role)) {
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}
