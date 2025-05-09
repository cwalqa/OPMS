<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\QuickBooksTokenService;

class QuickBooksServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(QuickBooksTokenService::class, function ($app) {
            return new QuickBooksTokenService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}