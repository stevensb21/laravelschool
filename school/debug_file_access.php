<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Method;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;

// Инициализируем Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ДИАГНОСТИКА ДОСТУПА К ФАЙЛАМ МЕТОДИКИ ===\n\n";

// 1. Проверяем существование папки storage
$storagePath = storage_path('app/public/methodfile');
echo "1. Проверка папки storage:\n";
echo "   Путь: {$storagePath}\n";
echo "   Существует: " . (file_exists($storagePath) ? 'ДА' : 'НЕТ') . "\n";
echo "   Права доступа: " . substr(sprintf('%o', fileperms($storagePath)), -4) . "\n\n";

// 2. Проверяем символическую ссылку
$publicStoragePath = public_path('storage');
echo "2. Проверка символической ссылки:\n";
echo "   Путь: {$publicStoragePath}\n";
echo "   Существует: " . (file_exists($publicStoragePath) ? 'ДА' : 'НЕТ') . "\n";
echo "   Это ссылка: " . (is_link($publicStoragePath) ? 'ДА' : 'НЕТ') . "\n";
if (is_link($publicStoragePath)) {
    echo "   Целевой путь: " . readlink($publicStoragePath) . "\n";
}
echo "\n";

// 3. Проверяем файлы методики
echo "3. Файлы методики в базе данных:\n";
$methods = Method::with('course')->get();
foreach ($methods as $method) {
    echo "   Метод ID {$method->id}: {$method->title}\n";
    echo "   Курс: " . ($method->course ? $method->course->name : 'НЕТ') . "\n";
    
    $fileFields = ['homework', 'lesson', 'exercise', 'book', 'presentation', 'test', 'article'];
    foreach ($fileFields as $field) {
        if (!empty($method->$field) && is_array($method->$field)) {
            foreach ($method->$field as $filePath) {
                if (strpos($filePath, '/storage/methodfile/') === 0) {
                    $relativePath = str_replace('/storage/methodfile/', '', $filePath);
                    $fullPath = storage_path('app/public/methodfile/' . $relativePath);
                    echo "     {$field}: {$filePath}\n";
                    echo "       Существует: " . (file_exists($fullPath) ? 'ДА' : 'НЕТ') . "\n";
                }
            }
        }
    }
    echo "\n";
}

// 4. Проверяем преподавателей и их доступ к курсам
echo "4. Преподаватели и их курсы:\n";
$teachers = Teacher::with('user')->get();
foreach ($teachers as $teacher) {
    echo "   Преподаватель: {$teacher->fio} (ID: {$teacher->users_id})\n";
    
    $courses = Course::where(function($q) use ($teacher) {
        $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
          ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
    })->get();
    
    foreach ($courses as $course) {
        echo "     Курс: {$course->name} (ID: {$course->id})\n";
        echo "     Access: " . json_encode($course->access_) . "\n";
    }
    echo "\n";
}

// 5. Проверяем студентов и их группы
echo "5. Студенты и их группы:\n";
$students = Student::with(['user', 'groups.courses'])->get();
foreach ($students as $student) {
    echo "   Студент: {$student->fio} (ID: {$student->users_id})\n";
    
    foreach ($student->groups as $group) {
        echo "     Группа: {$group->name} (ID: {$group->id})\n";
        foreach ($group->courses as $course) {
            // Проверяем, что $course является объектом, а не строкой
            if (is_object($course)) {
                echo "       Курс: {$course->name} (ID: {$course->id})\n";
            } else {
                echo "       Курс: {$course} (не объект)\n";
            }
        }
    }
    echo "\n";
}

// 6. Дополнительная диагностика - проверяем конкретный файл
echo "6. Проверка конкретного файла:\n";
$testFile = "presentation/1756127572_68ac6154c71aa.pdf";
$testFilePath = storage_path('app/public/methodfile/' . $testFile);
echo "   Тестовый файл: {$testFile}\n";
echo "   Полный путь: {$testFilePath}\n";
echo "   Существует: " . (file_exists($testFilePath) ? 'ДА' : 'НЕТ') . "\n";
echo "   Размер: " . (file_exists($testFilePath) ? filesize($testFilePath) . ' байт' : 'НЕТ') . "\n";
echo "   Права доступа: " . (file_exists($testFilePath) ? substr(sprintf('%o', fileperms($testFilePath)), -4) : 'НЕТ') . "\n";

// 7. Проверяем доступ через веб
echo "\n7. Проверка веб-доступа:\n";
$webPath = "/storage/methodfile/" . $testFile;
echo "   Веб-путь: {$webPath}\n";
echo "   Полный URL: http://your-domain.com{$webPath}\n";

// 8. Проверяем Apache статус
echo "\n8. Проверка Apache:\n";
$apacheStatus = shell_exec('systemctl is-active apache2 2>/dev/null');
echo "   Apache статус: " . trim($apacheStatus) . "\n";

if (trim($apacheStatus) !== 'active') {
    echo "   Ошибка Apache:\n";
    $apacheError = shell_exec('systemctl status apache2 2>&1 | tail -10');
    echo $apacheError;
}

echo "\n=== КОНЕЦ ДИАГНОСТИКИ ===\n"; 