<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        $admin = Auth::guard('web')->user();

        // Check if the admin has the Super Admin role or the specific permission
        if ($admin && ($admin->hasRole('Super Admin') || $admin->hasPermission($permission))) {
            return $next($request);
        }

        // If not, redirect or abort with unauthorized access
        return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
    }
}
