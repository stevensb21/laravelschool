<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Statistic;
use App\Models\HomeWorkStudent;
use App\Models\Student;
use App\Models\Course;
use App\Models\Group;
use App\Models\Teacher;

echo "=== Проверка данных ===\n\n";

echo "Статистика:\n";
echo "  - Всего записей: " . Statistic::count() . "\n";
if (Statistic::count() > 0) {
    $stats = Statistic::first();
    echo "  - Первая запись: ID студента {$stats->student_id}, оценка за урок {$stats->grade_lesson}, домашнее задание {$stats->homework}\n";
}

echo "\nДомашние задания студентов:\n";
echo "  - Всего записей: " . HomeWorkStudent::count() . "\n";
if (HomeWorkStudent::count() > 0) {
    $hw = HomeWorkStudent::first();
    echo "  - Первая запись: ID студента {$hw->student_id}, оценка {$hw->grade}\n";
}

echo "\nСтуденты:\n";
echo "  - Всего студентов: " . Student::count() . "\n";
if (Student::count() > 0) {
    $student = Student::first();
    echo "  - Первый студент: {$student->fio}, группа: {$student->group_name}\n";
}

echo "\nКурсы:\n";
echo "  - Всего курсов: " . Course::count() . "\n";
if (Course::count() > 0) {
    $course = Course::first();
    echo "  - Первый курс: {$course->name}, доступ: " . json_encode($course->access_) . "\n";
}

echo "\nГруппы:\n";
echo "  - Всего групп: " . Group::count() . "\n";
if (Group::count() > 0) {
    $group = Group::first();
    echo "  - Первая группа: {$group->name}\n";
}

echo "\nПреподаватели:\n";
$teachers = Teacher::all();
foreach ($teachers as $teacher) {
    echo "  - {$teacher->fio} (ID: {$teacher->users_id})\n";
    echo "    Предметы: " . json_encode($teacher->subjects) . "\n";
    
    // Проверяем курсы, которые ведет преподаватель
    $courseIds = Course::where(function($q) use ($teacher) {
        $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
          ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
    })->pluck('id')->toArray();
    
    echo "    Курсы (по access_): " . json_encode($courseIds) . "\n";
    
    // Проверяем группы
    $groupNames = [];
    if (!empty($courseIds)) {
        $groupNames = Group::where(function($q) use ($courseIds) {
            foreach ($courseIds as $cid) {
                $q->orWhereJsonContains('courses', (int)$cid)
                  ->orWhereJsonContains('courses', (string)$cid);
            }
        })->pluck('name')->toArray();
    }
    
    echo "    Группы: " . json_encode($groupNames) . "\n";
    
    // Проверяем студентов
    $studentIds = Student::whereIn('group_name', $groupNames)->pluck('id')->toArray();
    echo "    Студенты: " . json_encode($studentIds) . "\n";
    echo "\n";
} 