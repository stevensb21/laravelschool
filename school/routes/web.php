<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeWorkController;
use App\Http\Controllers\AppealController;
use App\Http\Controllers\MyFirstController;
use App\Http\Controllers\MethodController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Method;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;

use Illuminate\Support\Facades\Route;

// Teacher routes
Route::middleware(['auth'])->group(function () {
    Route::get('/teacher', [App\Http\Controllers\TeacherController::class, 'index'])->name('teacher');
    Route::post('/teacher/create', [App\Http\Controllers\TeacherController::class, 'store'])->name('teacher.store');
    Route::post('/teacher/edit', [App\Http\Controllers\TeacherController::class, 'edit'])->name('teacher.edit');
    Route::post('/teacher/delete', [App\Http\Controllers\TeacherController::class, 'delete'])->name('teacher.delete');
    Route::get('/teacher/account', [App\Http\Controllers\TeacherController::class, 'account'])->name('teacher.account');
    // Route::get('/teacher/statistics', [App\Http\Controllers\TeacherController::class, 'getStatistics'])->name('teacher.statistics');
    Route::get('/teacher/students', [App\Http\Controllers\TeacherController::class, 'students'])->name('teacher.students');
    Route::get('/teacher/lesson', [App\Http\Controllers\TeacherController::class, 'lesson'])->name('teacher.lesson');
    Route::get('/teacher/methodology', [App\Http\Controllers\TeacherController::class, 'methodology'])->name('teacher.methodology');
    Route::get('/teacher/homework', [App\Http\Controllers\TeacherController::class, 'homework'])->name('teacher.homework');
    Route::get('/teacher/appeals', [App\Http\Controllers\TeacherController::class, 'appeals'])->name('teacher.appeals');
    Route::post('/teacher/appeals/send', [App\Http\Controllers\TeacherController::class, 'sendAppeal'])->name('teacher.appeals.send');
    Route::put('/teacher/appeals/{id}/reply', [App\Http\Controllers\TeacherController::class, 'replyToAppeal'])->name('teacher.appeals.reply');
    Route::get('/teacher/calendar', [App\Http\Controllers\TeacherController::class, 'calendar'])->name('teacher.calendar');
    Route::post('/teacher/calendar/prev-week', [App\Http\Controllers\TeacherController::class, 'calendarPrevWeek'])->name('teacher.calendar.prev.week');
    Route::post('/teacher/calendar/next-week', [App\Http\Controllers\TeacherController::class, 'calendarNextWeek'])->name('teacher.calendar.next.week');
    Route::get('/teacher/student/{id}', [App\Http\Controllers\TeacherController::class, 'studentProfile'])->name('teacher.studentProfile');
    Route::get('/teacher/lesson/{lesson_id}/students', [App\Http\Controllers\TeacherController::class, 'lessonStudents'])->name('teacher.lesson.students');
    Route::post('/teacher/lesson/{lesson_id}/attendance', [App\Http\Controllers\TeacherController::class, 'saveLessonAttendance'])->name('teacher.lesson.attendance');
    
    // Маршруты для отзывов преподавателя
    Route::get('/teacher/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('teacher.reviews');
    Route::post('/teacher/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('teacher.reviews.store');
});

// Student routes
Route::middleware(['auth'])->group(function () {
    Route::get('/student', [App\Http\Controllers\StudentController::class, 'index'])->name('student');
    Route::post('/student/add', [App\Http\Controllers\StudentController::class, 'add'])->name('student.add');
    Route::post('/student/edit', [App\Http\Controllers\StudentController::class, 'edit'])->name('student.edit');
    Route::post('/student/delete', [App\Http\Controllers\StudentController::class, 'delete'])->name('student.delete');
    Route::post('/student/homework/{homework}', [App\Http\Controllers\StudentController::class, 'submitHomework'])->name('student.homework.submit');
    Route::get('/student/account', [App\Http\Controllers\StudentController::class, 'account'])->name('student.account');
    Route::get('/student/calendar', function() {
        return redirect()->route('calendar');
    })->name('student.calendar');
    Route::get('/student/homework', [App\Http\Controllers\StudentController::class, 'homework'])->name('student.homework');
    Route::get('/student/grades', [App\Http\Controllers\StudentController::class, 'grades'])->name('student.grades');
    Route::get('/student/attendance', [App\Http\Controllers\StudentController::class, 'attendance'])->name('student.attendance');
    Route::get('/student/appeals', [App\Http\Controllers\StudentController::class, 'appeals'])->name('student.appeals');
    Route::post('/student/appeals/send', [App\Http\Controllers\StudentController::class, 'sendAppeal'])->name('student.appeals.send');
    Route::put('/student/appeals/{id}/reply', [App\Http\Controllers\StudentController::class, 'replyToAppeal'])->name('student.appeals.reply');
    
    // Маршруты для отзывов студента
    Route::get('/student/reviews', [App\Http\Controllers\ReviewController::class, 'index'])->name('student.reviews');
    Route::post('/student/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('student.reviews.store');
});

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/management', [App\Http\Controllers\ManagementController::class, 'index'])->name('management');
    Route::post('/management/create-group', [App\Http\Controllers\ManagementController::class, 'createGroup'])->name('management.createGroup');
    Route::put('/management/update-group', [App\Http\Controllers\ManagementController::class, 'updateGroup'])->name('management.updateGroup');
    Route::post('/management/delete-group', [App\Http\Controllers\ManagementController::class, 'deleteGroup'])->name('management.deleteGroup');
    Route::get('/management/get-group-data', [App\Http\Controllers\ManagementController::class, 'getGroupData'])->name('management.getGroupData');
    Route::post('/management/create-course', [App\Http\Controllers\ManagementController::class, 'createCourse'])->name('management.createCourse');
    Route::put('/management/update-course', [App\Http\Controllers\ManagementController::class, 'updateCourse'])->name('management.updateCourse');
    Route::post('/management/delete-course', [App\Http\Controllers\ManagementController::class, 'deleteCourse'])->name('management.deleteCourse');
    Route::post('/management/backup', [App\Http\Controllers\ManagementController::class, 'createBackup'])->name('management.backup');
    Route::post('/management/restore-backup', [App\Http\Controllers\ManagementController::class, 'restoreBackup'])->name('management.restoreBackup');
    Route::get('/management/backups-list', [App\Http\Controllers\ManagementController::class, 'getBackupsList'])->name('management.getBackupsList');
    Route::get('/management/get-course-data', [App\Http\Controllers\ManagementController::class, 'getCourseData'])->name('management.getCourseData');
    Route::get('/admin/teacher/{id}/profile', [App\Http\Controllers\TeacherController::class, 'adminProfile'])->name('admin.teacher.profile');
    
    // Маршруты для отзывов
    Route::get('/admin/reviews', [App\Http\Controllers\ReviewController::class, 'adminIndex'])->name('admin.reviews.index');
    Route::post('/admin/reviews/{id}/approve', [App\Http\Controllers\ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::post('/admin/reviews/{id}/reject', [App\Http\Controllers\ReviewController::class, 'reject'])->name('admin.reviews.reject');
    Route::delete('/admin/reviews/{id}', [App\Http\Controllers\ReviewController::class, 'destroy'])->name('admin.reviews.destroy');
    Route::post('/management/delete-backup', [App\Http\Controllers\ManagementController::class, 'deleteBackup'])->name('management.deleteBackup');
    Route::get('/management/auto-backup-settings', [App\Http\Controllers\ManagementController::class, 'getAutoBackupSettings']);
    Route::post('/management/auto-backup-settings', [App\Http\Controllers\ManagementController::class, 'saveAutoBackupSettings']);
    
    // Маршруты для управления группами студентов
    Route::get('/admin/student/{id}/groups', [App\Http\Controllers\StudentGroupController::class, 'index'])->name('admin.student.groups');
    Route::post('/admin/student/{id}/add-to-group', [App\Http\Controllers\StudentGroupController::class, 'addToGroup'])->name('admin.student.add-to-group');
    Route::post('/admin/student/{id}/remove-from-group', [App\Http\Controllers\StudentGroupController::class, 'removeFromGroup'])->name('admin.student.remove-from-group');
    Route::post('/admin/student/{id}/set-primary-group', [App\Http\Controllers\StudentGroupController::class, 'setPrimaryGroup'])->name('admin.student.set-primary-group');
    
    Route::get('/admin/group/{id}/students', [App\Http\Controllers\StudentGroupController::class, 'groupStudents'])->name('admin.group.students');
    Route::post('/admin/group/{id}/add-student', [App\Http\Controllers\StudentGroupController::class, 'addStudentToGroup'])->name('admin.group.add-student');
    Route::post('/admin/group/{id}/remove-student', [App\Http\Controllers\StudentGroupController::class, 'removeStudentFromGroup'])->name('admin.group.remove-student');
    
    Route::get('/admin/groups', [App\Http\Controllers\StudentGroupController::class, 'groupsList'])->name('admin.groups.list');
});

Route::get('/', [HomeController::class,'index']);


Route::get('/calendar', [CalendarController::class, 'index'])->middleware('auth')->name('calendar');
Route::post('/calendar/prev-week', [CalendarController::class, 'prevWeek'])->middleware('auth')->name('calendar.prev.week');
Route::post('/calendar/next-week', [CalendarController::class, 'nextWeek'])->middleware('auth')->name('calendar.next.week');
Route::get('/calendar/create', [CalendarController::class, 'AddRow'])->middleware('auth');
Route::get('/calendar/edit', [CalendarController::class, 'editMode'])->middleware('auth')->name('calendar.edit');
Route::post('/calendar/add-lesson', [CalendarController::class, 'addLesson'])->middleware('auth')->name('calendar.add-lesson');
Route::post('/calendar/delete-lesson', [CalendarController::class, 'deleteLesson'])->middleware('auth')->name('calendar.delete-lesson');


Route::get('/homework', [HomeWorkController::class, 'index'])->middleware('auth')->name('homework');
Route::get('/homework/{id}/submissions', [HomeWorkController::class, 'submissions'])->middleware('auth')->name('homework.submissions');
Route::post('/homework/grade', [HomeWorkController::class, 'grade'])->middleware('auth')->name('homework.grade');
Route::post('/homework/{id}/extend-deadline', [HomeWorkController::class, 'extendDeadline'])->middleware('auth')->name('homework.extend-deadline');
Route::delete('/homework/{id}', [HomeWorkController::class, 'destroy'])->middleware('auth')->name('homework.destroy');

Route::get('/appeals', [AppealController::class, 'index'])->middleware('auth')->name('appeals');
Route::get('/appeals/{id}', [AppealController::class, 'show'])->middleware('auth')->name('appeals.show');
Route::post('/appeals', [AppealController::class, 'store'])->middleware('auth')->name('appeals.store');
Route::put('/appeals/{id}', [AppealController::class, 'update'])->middleware('auth')->name('appeals.update');
Route::delete('/appeals/{id}', [AppealController::class, 'destroy'])->middleware('auth')->name('appeals.destroy');


Route::get('/statistic', [StatisticController::class, 'index'])->middleware('auth')->name('statistic');
Route::get('/statistic/add-test-data', [StatisticController::class, 'addTestData'])->middleware('auth');
Route::get('/statistic/export', [App\Http\Controllers\StatisticController::class, 'export'])->middleware('auth')->name('statistic.export');


Route::get('/method', [MethodController::class, 'index'])->middleware('auth')->name('method');
Route::get('/method/edit', [MethodController::class, 'editMode'])->middleware('auth')->name('method.edit');
Route::post('/method/delete', [MethodController::class, 'delete'])->middleware('auth')->name('method.delete');
Route::post('/method/store', [MethodController::class, 'store'])->middleware('auth')->name('method.store');
Route::post('/method/create', [MethodController::class, 'create'])->middleware('auth')->name('method.create');
Route::post('/method/update', [MethodController::class, 'update'])->middleware('auth')->name('method.update');

// Маршрут для обслуживания файлов
Route::get('/storage/{path}', function($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeType = mime_content_type($filePath);
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"'
        ]);
    }
    
    abort(404);
})->where('path', '.*')->middleware('auth');

Route::get( '/account', [AccountController::class, 'index'])->middleware('auth')->name('account.index');




Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


Route::get('/createmethod', function() {
    $method = Method::create([  
        'course_id' => 1,
        'title' => "C++ Типы данных",
        'title_homework' => ["Homework 2"],
        'homework' => ["methodfil/homework/44.pdf"],
        'title_lesson' => ["Lesson 2"],
        'lesson' => ["methodfile/lesson/34.pdf"],
        'title_exercise' => null,
        'exercise' => null,
        'title_book' => ["Book 2"],
        'book' => ["methodfile/book/Algebra_merged.pdf"],
        'title_video' => ["Video 2"],
        'video' => ["https://rutube.ru/"],
        'title_presentation' => ["Presentation 2"],
        'presentation' => ["methodfile/presentation/24.pdf"],
        'title_test' => ["Test 2"],
        'test' => ["methodfile/test/MatAnalyz_merged.pdf"],
        'title_article' => ["Статья 2", "Статья 3"],
        'article' => ["methodfile/article/MatAnalyz.pdf", "https://workspace.google.com/intl/ru/products/docs/"]
    ]);
    return 'Method успешно создан';

});

Route::get('/createhomework', function() {
    $hm = new App\Models\HomeWork();
    $hm->name = 'Python Введение'; // или любое другое имя пользователя
    $hm->groups_id = 1; // или любой другой email
    $hm->teachers_id = 2;
    $hm->course_id = 1; // или любая другая роль
    $hm->method_id = 1;
    $hm->deadline = '2025-08-12';
    $hm->description = null;
    $hm->file_path = '\/storage\/methodfile\/homework\/1750725442_6859f342713ea.pdf';
    $hm->save();
    return 'Пользователь успешно создан';
});

Route::get('/createhomestudent', function() {
    $hm = new App\Models\HomeWorkStudent();
   
    $hm->home_work_id = 4; // или любой другой email
    $hm->student_id = 1;
    $hm->file_path ='\/storage\/methodfile\/homework\/1750725442_6859f342713ea.pdf'; // или любая другая роль
    $hm->grade = null;
    $hm->feedback = null;
    $hm->save();
    return 'Пользователь успешно создан';
});


Route::get('/createuser', function() {
    $user = new App\Models\User();
    $user->name = 'admin'; // или любое другое имя пользователя
    $user->email = 'admin@example.com'; // или любой другой email
    $user->password = Hash::make('admin');
    $user->role = 'admin'; // или любая другая роль
    $user->save();
    return 'Пользователь успешно создан';
});

Route::get('/createuserstudent', function() {
    $user = new App\Models\User();
    $user->name = 'student'; // или любое другое имя пользователя
    $user->email = 'student@example.com'; // или любой другой email
    $user->password = Hash::make('student');
    $user->role = 'student'; // или любая другая роль
    $user->save();
    return 'Пользователь student успешно создан';
});

Route::get('/createuserteacher', function() {
    $user = new App\Models\User();
    $user->name = 'teacher1'; // или любое другое имя пользователя
    $user->email = 'teacher1@example.com'; // или любой другой email
    $user->password = Hash::make('teacher');
    $user->role = 'teacher'; // или любая другая роль
    $user->save();
    return 'Пользователь teacher успешно создан';

});


Route::get('/createcourse', function() {
    $course = new App\Models\Course();
    $course->name = 'C++';
    $course->access_ = json_encode([
        'groups' => [],
        'teachers' => ['admin']
    ]);
    $course->save();
    return 'Новый курс успешно создан';
});

Route::get('/createmorecourses', function() {
    $courses = [
        ['name' => 'Python', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])],
        ['name' => 'JavaScript', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])],
        ['name' => 'Java', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])],
        ['name' => 'PHP', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])]
    ];
    
    foreach ($courses as $courseData) {
        $course = new App\Models\Course();
        $course->name = $courseData['name'];
        $course->access_ = $courseData['access_'];
        $course->save();
    }
    
    return 'Дополнительные курсы успешно созданы';
});


Route::get('/creategroup', function() {
    $group = new App\Models\Group();
    $group->name = '141-21';
    $group->size = 0;
    $group->save();
    return 'Новая группа успешно создана';
});





Route::get('/linkcoursetogroup', function() {
    $group = App\Models\Group::where('name', '141-21')->first();
    $course = App\Models\Course::where('name', 'C++')->first();
    
    if ($group && $course) {
        $group->courses()->attach($course->id);
        return 'Курс C++ успешно связан с группой 141-21';
    } else {
        return 'Группа или курс не найдены';
    }
});

Route::get('/linkmorecoursestogroup', function() {
    $group = App\Models\Group::where('name', '141-21')->first();
    $courses = App\Models\Course::whereIn('name', ['Python', 'JavaScript', 'Java'])->get();
    
    if ($group && $courses->count() > 0) {
        $courseIds = $courses->pluck('id')->toArray();
        $group->courses()->attach($courseIds);
        return 'Курсы Python, JavaScript, Java успешно связаны с группой 141-21';
    } else {
        return 'Группа или курсы не найдены';
    }
});

Route::get('/testgroupcourses', function() {
    $group = App\Models\Group::where('name', '141-21')->first();
    
    if ($group) {
        $courseNames = $group->getCourseNames();
        return 'Курсы группы 141-21: ' . implode(', ', $courseNames);
    } else {
        return 'Группа не найдена';
    }
});

Route::get('/cleanorphanedstudents', function() {
    $orphanedStudents = App\Models\Student::whereNotExists(function($query) {
        $query->select(\DB::raw(1))
              ->from('users')
              ->whereRaw('users.id = students.users_id');
    })->get();
    
    $count = $orphanedStudents->count();
    $orphanedStudents->each(function($student) {
        $student->delete();
    });
    
    return "Удалено {$count} студентов без связанных пользователей";
});


Route::get('/createteacher', function() {
    $teacher = new App\Models\Teacher();
    $user = User::where('name', 'teacher1')->first();
    
    $teacher = $user->teacher()->create([
        'fio' => 'Абрамова Регина Александровна',
        'job_title' => 'Python Teacher',
        'email' => 'teacher@gmail.com',
        'subjects' => ['C++'],
        'education' => [],
        'achievements' => [],
        'average_performance' => 0,
        'average_attendance' => 0,
        'average_exam_score' => 0
    ]);
    return 'Новый преподаватель успешно создан';
});

// $table->id();
// $table->timestamps();
// $table->integer('users_id');
// $table->string('fio');
// $table->string('job_title');
// $table->string('email');
// $table->double('average_performance');
// $table->double('average_attendance');
// $table->double('average_exam_score');
// $table->json('subjects');
// $table->json('education');
// $table->json('achievements');

Route::get('/createappeal', function() {
    $appeal = new App\Models\Appeal();
    $appeal->title = 'Тестовое обращение student->admin';
    $appeal->sender_id = 6; // ID отправителя (например, студент)
    $appeal->recipient_id = 4; // ID получателя (например, преподаватель)
    $appeal->type = 'Вопрос';
    $appeal->description = 'Это тестовое обращение для проверки функциональности';
    $appeal->feedback = null;
    $appeal->like_feedback = null;
    $appeal->status = 'Активно';
    $appeal->save();
    
    return 'Тестовое обращение успешно создано с ID: ' . $appeal->id;
});

// Тестовые маршруты для проверки бэкапа/восстановления
Route::get('/test/delete-records', function() {
    try {
        // Удаляем несколько записей для тестирования
        $deletedGroups = App\Models\Group::whereIn('name', ['141-21'])->delete();
        $deletedCourses = App\Models\Course::whereIn('name', ['Python', 'JavaScript'])->delete();
        $deletedStudents = App\Models\Student::limit(2)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Тестовые записи удалены',
            'deleted' => [
                'groups' => $deletedGroups,
                'courses' => $deletedCourses,
                'students' => $deletedStudents
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test/show-records', function() {
    try {
        $groups = App\Models\Group::all(['name']);
        $courses = App\Models\Course::all(['name']);
        $students = App\Models\Student::count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'groups' => $groups->pluck('name')->toArray(),
                'courses' => $courses->pluck('name')->toArray(),
                'students_count' => $students
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test/restore-test-data', function() {
    try {
        // Восстанавливаем тестовые данные
        $group = new App\Models\Group();
        $group->name = '141-21';
        $group->size = 0;
        $group->save();
        
        $courses = [
            ['name' => 'Python', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])],
            ['name' => 'JavaScript', 'access_' => json_encode(['groups' => [], 'teachers' => ['admin']])]
        ];
        
        foreach ($courses as $courseData) {
            $course = new App\Models\Course();
            $course->name = $courseData['name'];
            $course->access_ = $courseData['access_'];
            $course->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Тестовые данные восстановлены'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/admin/student/{id}', [App\Http\Controllers\StudentController::class, 'adminView'])->name('admin.student.view')->middleware('auth');

// Маршруты для отзывов (для всех пользователей)
Route::middleware(['auth'])->group(function () {
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{recipientType}/{recipientId}', [App\Http\Controllers\ReviewController::class, 'index'])->name('reviews.index');
});

Route::get('/fix-courses-json', function() {
    $fixed = 0;
    $courses = \DB::table('courses')->get();
    foreach ($courses as $course) {
        $access = $course->access_;
        // Если это строка с экранированными кавычками — пробуем декодировать
        if (is_string($access) && preg_match('/^\s*\\?\"?\{.*\}\"?\s*$/s', $access)) {
            $decoded = json_decode($access, true);
            // Если декодировалось в строку — значит, это строка внутри строки
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            if (is_array($decoded)) {
                \DB::table('courses')->where('id', $course->id)->update([
                    'access_' => json_encode($decoded, JSON_UNESCAPED_UNICODE)
                ]);
                $fixed++;
            }
        }
    }
    return "Исправлено $fixed курсов.";
});

Route::get('/debug-teacher-structure', function() {
    $result = [];
    $teachers = \App\Models\Teacher::with('user')->get();
    foreach ($teachers as $teacher) {
        $teacherData = [
            'users_id' => $teacher->users_id,
            'fio' => $teacher->fio,
            'email' => $teacher->email,
        ];
        // Курсы, где этот преподаватель есть в access_->teachers
        $courses = \App\Models\Course::where(function($q) use ($teacher) {
            $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
              ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
        })->get();
        $teacherData['courses'] = $courses->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'access_' => $c->access_,
            ];
        });
        // Группы, где есть хотя бы один из этих курсов
        $courseIds = $courses->pluck('id')->toArray();
        $groups = \App\Models\Group::where(function($q) use ($courseIds) {
            foreach ($courseIds as $cid) {
                $q->orWhereJsonContains('courses', (int)$cid)
                  ->orWhereJsonContains('courses', (string)$cid);
            }
        })->get();
        $teacherData['groups'] = $groups->map(function($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'courses' => $g->courses,
            ];
        });
        // Студенты из этих групп
        $groupNames = $groups->pluck('name')->toArray();
        $students = \App\Models\Student::whereIn('group_name', $groupNames)->get();
        $teacherData['students'] = $students->map(function($s) {
            return [
                'id' => $s->id,
                'fio' => $s->fio,
                'group_name' => $s->group_name,
            ];
        });
        $result[] = $teacherData;
    }
    return response()->json($result);
});

Route::get('/cleanup-orphan-users', function() {
    $deleted = \DB::table('users')
        ->whereNotIn('id', function($q) {
            $q->select('users_id')->from('teachers');
        })
        ->whereNotIn('id', function($q) {
            $q->select('users_id')->from('students');
        })
        ->where('role', '!=', 'admin')
        ->delete();
    return 'Удалено пользователей: ' . $deleted;
});

// --- ЧАТЫ ---
Route::middleware(['auth'])->group(function () {
    Route::get('/chats', [ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats/{chat}/send', [ChatController::class, 'sendMessage'])->name('chats.send');
    Route::post('/chats/{chat}/clear', [ChatController::class, 'clearHistory'])->name('chats.clear');
    Route::get('/chats/{chat}/fetch', [ChatController::class, 'fetchMessages'])->name('chats.fetch');
    Route::post('/chats/create-teachers-chat', [ChatController::class, 'createTeachersChat'])->name('chats.createTeachersChat');
});