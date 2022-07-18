<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DailyUpdate::class
    ];


    protected $routeMiddleware = [
            'jwt.verify' => \App\Http\Middleware\JwtMiddleware::class,
            'jwt.auth' => 'Tymon\JWTAuth\Middleware\GetUserFromToken',
            'jwt.refresh' => 'Tymon\JWTAuth\Middleware\RefreshToken',
            ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('daily:update')
        ->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}