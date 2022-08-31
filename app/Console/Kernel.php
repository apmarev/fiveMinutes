<?php

namespace App\Console;

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AmoCrmController;
use App\Http\Controllers\UnisenderController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        Commands\DataReport::class
    ];
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->call(function() {
//            $amo = new UnisenderController();
//            $amo->getSheet();
//        })
//            ->dailyAt('23:00');

        $schedule->call(function() {
            $amo = new AmoCrmController(new AccessController());
            $amo->generate_data();
        })
            ->dailyAt('03:00');

//        $schedule->call(function() {
//            $amo = new AmoCrmController(new AccessController());
//            $amo->getSenlerQueues();
//        })
//            ->everyFourHours();
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
