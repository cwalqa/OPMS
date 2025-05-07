<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionExpiredMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            // Check if the session is expired
            if ($request->expectsJson()) {
                // If it's an AJAX request, return a JSON response
                return response()->json(['session_expired' => true], 401);
            } else {
                // Otherwise, redirect to the login page
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}