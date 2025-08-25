<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Method;
use App\Models\Course;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;

// Загружаем переменные окружения
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Настраиваем подключение к базе данных
$config = [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'database' => $_ENV['DB_DATABASE'] ?? 'laravel',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

DB::purge();
DB::reconnect($config);

echo "=== ДИАГНОСТИКА БАЗЫ ДАННЫХ ===\n\n";

// Проверяем методы
echo "=== МЕТОДЫ ===\n";
$methods = Method::with('course')->get();

foreach ($methods as $method) {
    echo "ID: {$method->id}, Курс: " . ($method->course ? $method->course->name : 'N/A') . "\n";
    
    $fileFields = ['homework', 'lesson', 'exercise', 'book', 'presentation', 'test', 'article'];
    
    foreach ($fileFields as $field) {
        if (!empty($method->$field)) {
            echo "  {$field}: " . json_encode($method->$field) . "\n";
        }
    }
    echo "\n";
}

// Проверяем курсы
echo "=== КУРСЫ ===\n";
$courses = Course::all();

foreach ($courses as $course) {
    echo "ID: {$course->id}, Название: {$course->name}\n";
    echo "  Доступ преподавателей: " . json_encode($course->access_) . "\n";
    echo "\n";
}

// Проверяем группы
echo "=== ГРУППЫ ===\n";
$groups = Group::with('courses')->get();

foreach ($groups as $group) {
    echo "ID: {$group->id}, Название: {$group->name}\n";
    echo "  Курсы: ";
    if (is_array($group->courses)) {
        foreach ($group->courses as $course) {
            if (is_object($course)) {
                echo "{$course->name} (ID: {$course->id}), ";
            } else {
                echo "{$course}, ";
            }
        }
    }
    echo "\n\n";
}

// Проверяем студентов
echo "=== СТУДЕНТЫ ===\n";
$students = Student::with('groups')->get();

foreach ($students as $student) {
    echo "ID: {$student->id}, Имя: {$student->name}\n";
    echo "  Группы: ";
    foreach ($student->groups as $group) {
        echo "{$group->name} (ID: {$group->id}), ";
    }
    echo "\n\n";
}

// Проверяем преподавателей
echo "=== ПРЕПОДАВАТЕЛИ ===\n";
$teachers = Teacher::all();

foreach ($teachers as $teacher) {
    echo "ID: {$teacher->id}, Имя: {$teacher->name}, User ID: {$teacher->users_id}\n";
    echo "\n";
}

echo "=== КОНЕЦ ДИАГНОСТИКИ ===\n"; 