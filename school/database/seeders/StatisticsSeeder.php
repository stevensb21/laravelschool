<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Statistic;
use App\Models\Student;

class StatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Получаем всех студентов
        $students = Student::all();
        
        if ($students->count() == 0) {
            $this->command->info('Нет студентов в базе данных. Создайте сначала студентов.');
            return;
        }

        // Создаем тестовые записи статистики
        foreach ($students as $student) {
            // Создаем несколько записей для каждого студента
            for ($i = 0; $i < rand(5, 15); $i++) {
                Statistic::create([
                    'student_id' => $student->id,
                    'grade_lesson' => rand(0, 5) > 0 ? round(rand(20, 50) / 10, 1) : 0, // 0 или оценка от 2.0 до 5.0
                    'homework' => rand(0, 5) > 0 ? round(rand(20, 50) / 10, 1) : 0, // 0 или оценка от 2.0 до 5.0
                    'attendance' => rand(0, 5) > 0, // true/false
                    'notes' => rand(0, 1) ? 'Хорошая работа на уроке' : null,
                ]);
            }
        }

        $this->command->info('Тестовые данные статистики созданы успешно!');
    }
}
