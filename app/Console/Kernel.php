<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // You can register your command here
        \App\Console\Commands\UpdateUserDetails::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the command to run every minute
        $schedule->command('update:user-details')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        // Include any other command files here
        require base_path('routes/console.php');
    }
}
