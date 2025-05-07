<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after login.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and rate limiters.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Define route groups for web and api
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        });
    }

    /**
     * Define application-specific rate limiters.
     */
    protected function configureRateLimiting(): void
{
    // Customer login rate limit
    RateLimiter::for('login', function (Request $request) {
        $email = (string) $request->email;
        return Limit::perMinute(5)->by('customer|'.$email.'|'.$request->ip());
    });

    // Admin login rate limit
    RateLimiter::for('admin-login', function (Request $request) {
        $email = (string) $request->email;
        return Limit::perMinute(5)->by('admin|'.$email.'|'.$request->ip());
    });

    // 2FA validation limit
    RateLimiter::for('2fa', function (Request $request) {
        return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
    });

    // 2FA resend limit
    RateLimiter::for('resend-2fa', function (Request $request) {
        return Limit::perMinutes(3, 1)->by($request->user()?->id ?: $request->ip());
    });
}
}
