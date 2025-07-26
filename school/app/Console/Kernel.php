<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\AutoBackup::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Запускать команду каждую минуту, она сама решит, нужно ли делать бэкап
        $schedule->command('backup:auto')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
} 