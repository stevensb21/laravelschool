<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HomeWork;
use Carbon\Carbon;

class UpdateHomeworkStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'homework:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет статус всех домашних заданий на основе текущей даты и прогресса';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление статуса домашних заданий...');
        
        $updated = HomeWork::updateAllStatuses();
        
        $this->info("Обновление завершено! Обновлено заданий: {$updated}");
        
        return 0;
    }
}
