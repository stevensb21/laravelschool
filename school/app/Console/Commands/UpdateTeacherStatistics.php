<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\Statistic;
use App\Models\HomeWorkStudent;

class UpdateTeacherStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teachers:update-statistics {--teacher-id= : ID конкретного преподавателя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет статистику преподавателей на основе оценок студентов из их групп';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем обновление статистики преподавателей...');

        $teacherId = $this->option('teacher-id');
        
        if ($teacherId) {
            $teachers = Teacher::where('users_id', $teacherId)->get();
            if ($teachers->isEmpty()) {
                $this->error("Преподаватель с ID {$teacherId} не найден!");
                return 1;
            }
        } else {
            $teachers = Teacher::all();
        }

        $bar = $this->output->createProgressBar($teachers->count());
        $bar->start();

        foreach ($teachers as $teacher) {
            $this->updateTeacherStatistics($teacher);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Статистика преподавателей успешно обновлена!');

        return 0;
    }

    private function updateTeacherStatistics(Teacher $teacher)
    {
        // Используем метод из модели для вычисления статистики
        $statistics = $teacher->calculateAndUpdateStatistics();
        
        $this->line("Преподаватель {$teacher->fio}: Успеваемость: {$statistics['average_performance']}, Посещаемость: {$statistics['average_attendance']}%, Экзамены: {$statistics['average_exam_score']}");
    }
}
