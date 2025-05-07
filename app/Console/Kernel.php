<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\ProductionSchedule;
use App\Notifications\ScheduleDeadlineReminder;
use Illuminate\Support\Facades\Notification;    

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
        Commands\GenerateQuickbooksAccessToken::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            // Other middleware
            \Illuminate\Session\Middleware\StartSession::class,
            // Other middleware
        ],
    ];

    protected $routeMiddleware = [
        // Other middleware
        'session.expired' => \App\Http\Middleware\SessionExpiredMiddleware::class,
        'role' => \App\Http\Middleware\CheckRole::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('quickbooks:generate-access-token')->everyTwentyMinutes(); // Adjust the frequency as needed


        $schedule->call(function () {
            $tomorrow = now()->addDay()->format('Y-m-d');
            
            // Find all schedules with a deadline of tomorrow
            $schedules = ProductionSchedule::where('deadline_date', $tomorrow)->get();
            
            // Send reminder notifications to production leads
            foreach ($schedules as $schedule) {
                $productionLead = $schedule->line->lineManager;  // Assuming line manager is the production lead
                $productionLead->notify(new ScheduleDeadlineReminder($schedule));
            }
        })->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
