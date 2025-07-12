<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== КУРСЫ ===\n";
$courses = \App\Models\Course::all(['id', 'name']);
foreach ($courses as $course) {
    echo "ID: {$course->id}, Название: {$course->name}\n";
}

echo "\n=== МЕТОДПАКЕТЫ ===\n";
$methods = \App\Models\Method::with('course')->get();
foreach ($methods as $method) {
    $courseName = $method->course ? $method->course->name : 'без курса';
    echo "ID: {$method->id}, Курс: {$courseName}, Название: {$method->title}\n";
    if ($method->title_homework) {
        echo "  Домашние задания: " . implode(', ', $method->title_homework) . "\n";
    }
}

echo "\n=== УРОКИ НА СЕГОДНЯ ===\n";
$today = date('Y-m-d');
$lessons = \App\Models\Calendar::where('date_', $today)->get();
foreach ($lessons as $lesson) {
    echo "ID: {$lesson->id}, Предмет: {$lesson->subject}, Группа: {$lesson->name_group}\n";
} 