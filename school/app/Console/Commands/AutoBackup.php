<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Автоматическое создание резервной копии по расписанию';

    public function handle()
    {
        $settingsPath = storage_path('app/auto_backup_settings.json');
        if (!file_exists($settingsPath)) {
            $this->info('Автобэкап не настроен.');
            return 0;
        }
        $settings = json_decode(file_get_contents($settingsPath), true);
        if (empty($settings['enabled'])) {
            $this->info('Автобэкап выключен.');
            return 0;
        }
        $now = now();
        $shouldRun = false;
        $period = $settings['period'] ?? 'daily';
        $time = $settings['time'] ?? '03:00';
        [$hour, $minute] = explode(':', $time);
        if ($period === 'daily') {
            $shouldRun = $now->format('H:i') === $time;
        } elseif ($period === 'weekly') {
            $shouldRun = $now->dayOfWeek === 1 && $now->format('H:i') === $time; // Пн
        } elseif ($period === 'monthly') {
            $shouldRun = $now->day === 1 && $now->format('H:i') === $time;
        }
        if ($shouldRun) {
            $service = new BackupService();
            $result = $service->createBackup();
            if ($result['success']) {
                $this->info('Автоматический бэкап успешно создан: ' . ($result['backup_name'] ?? ''));
            } else {
                $this->error('Ошибка при создании автобэкапа: ' . ($result['error'] ?? ''));
            }
        } else {
            $this->info('Не время для автобэкапа: ' . $now->format('Y-m-d H:i'));
        }
        return 0;
    }
} 