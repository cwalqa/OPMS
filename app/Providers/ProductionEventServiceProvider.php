<?php

namespace App\Providers;

use App\Events\ProductionStageChanged;
use App\Listeners\LogProductionChange;
use App\Listeners\NotifyOrderStatusChange;
use App\Listeners\CheckOrderCompletion;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class ProductionEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ProductionStageChanged::class => [
            LogProductionChange::class,
            NotifyOrderStatusChange::class,
            CheckOrderCompletion::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}