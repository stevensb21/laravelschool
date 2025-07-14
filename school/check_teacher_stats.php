<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Teacher;

echo "=== Проверка статистики преподавателей ===\n\n";

$teachers = Teacher::all();

foreach ($teachers as $teacher) {
    echo "Преподаватель: {$teacher->fio}\n";
    echo "  - Средняя успеваемость: {$teacher->average_performance}\n";
    echo "  - Средняя посещаемость: {$teacher->average_attendance}%\n";
    echo "  - Средняя за экзамены: {$teacher->average_exam_score}\n";
    echo "\n";
}

echo "=== Обновление статистики ===\n\n";

foreach ($teachers as $teacher) {
    $stats = $teacher->calculateAndUpdateStatistics();
    echo "Обновлено для {$teacher->fio}:\n";
    echo "  - Успеваемость: {$stats['average_performance']}\n";
    echo "  - Посещаемость: {$stats['average_attendance']}%\n";
    echo "  - Экзамены: {$stats['average_exam_score']}\n";
    echo "\n";
} 