<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GROUPS DATA ===\n";
$groups = \App\Models\Group::all();
foreach ($groups as $group) {
    echo "Group ID: {$group->id}, Name: {$group->name}, Courses: " . json_encode($group->courses) . "\n";
}

echo "\n=== COURSES DATA ===\n";
$courses = \App\Models\Course::all();
foreach ($courses as $course) {
    echo "Course ID: {$course->id}, Name: {$course->name}, Access: " . json_encode($course->access_) . "\n";
}

echo "\n=== TEACHER DATA ===\n";
$teacher = \App\Models\Teacher::where('users_id', 2)->first();
if ($teacher) {
    echo "Teacher ID: {$teacher->id}, Users ID: {$teacher->users_id}, FIO: {$teacher->fio}\n";
    
    // Находим курсы преподавателя
    $teacherCourses = \App\Models\Course::whereJsonContains('access_->teachers', (int)$teacher->users_id)->get();
    echo "Teacher courses count: " . $teacherCourses->count() . "\n";
    echo "Teacher courses IDs: " . $teacherCourses->pluck('id')->implode(', ') . "\n";
    
    // Находим группы для этих курсов
    foreach ($teacherCourses as $course) {
        echo "Course {$course->name} (ID: {$course->id}):\n";
        $courseGroups = \App\Models\Group::where(function($q) use ($course) {
            $q->orWhereJsonContains('courses', (int)$course->id);
        })->get();
        
        echo "  Groups count: " . $courseGroups->count() . "\n";
        if ($courseGroups->count() > 0) {
            foreach ($courseGroups as $group) {
                echo "  - Group: {$group->name} (ID: {$group->id})\n";
            }
        } else {
            echo "  - No groups found\n";
        }
    }
    
    // Проверяем все группы
    echo "\n=== ALL GROUPS FOR TEACHER ===\n";
    $allGroups = \App\Models\Group::where(function($q) use ($teacherCourses) {
        foreach ($teacherCourses as $course) {
            $q->orWhereJsonContains('courses', (int)$course->id);
        }
    })->pluck('name');
    
    echo "All groups for teacher: " . $allGroups->implode(', ') . "\n";
} 