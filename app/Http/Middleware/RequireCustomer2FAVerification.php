<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RequireCustomer2FAVerification
{
    public function handle(Request $request, Closure $next)
    {
        // If user is logged in but hasn't verified 2FA
        if (Auth::guard('web')->check() && Session::get('two_factor_verified') !== true) {
            return redirect()->route('customer.2fa')
                ->with('warning', 'Please complete two-factor authentication.');
        }

        return $next($request);
    }
}