<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RedirectIfAuthenticatedCustomer
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in BUT has NOT verified 2FA
        if (Auth::guard('web')->check() && Session::get('two_factor_verified') !== true) {
            // Redirect to 2FA page if not verified
            return redirect()->route('customer.2fa');
        }

        // If not logged in or already verified, continue to next middleware
        return $next($request);
    }
}