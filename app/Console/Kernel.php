<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->command('report:generate-weekly-stipend')
        //     ->everyMinute() // Monday 12:00 AM
        //     ->withoutOverlapping();
        
        $schedule->command('report:generate-weekly-stipend')
            ->weeklyOn(6, '00:00') // Saturday 12:00 AM
            ->withoutOverlapping();

        $schedule->command('report:weekly-compensation')
            ->weeklyOn(1, '00:00') // Monday 12:00 AM
            ->withoutOverlapping();

        $schedule->command('report:export-weekly-stipend')
        ->weeklyOn(2, '00:00'); // Tuesday 12:00 AM

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
