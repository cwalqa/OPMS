<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware\RedirectIfUnauthenticated;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        if (app()->environment('production')) {
            URL::forceScheme('https');
            app(Illuminate\Http\Response::class)->headers->set(
                'Content-Security-Policy',
                "default-src 'self'; script-src 'self'; object-src 'none';"
            );
        }
    }
}
