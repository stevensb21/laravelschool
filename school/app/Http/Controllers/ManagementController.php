<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\HomeWork;
use App\Models\Statistic;
use App\Services\BackupService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ManagementController extends Controller
{
    public function index() {
        // Статистика групп
        $groups = Group::with('students')->get();
        $groupsList = $groups->pluck('name')->toArray();
        $totalGroups = $groups->count();
        $totalStudentsInGroups = $groups->sum(function($group) {
            return $group->students->count();
        });

        // Статистика курсов
        $courses = Course::with(['groups.students'])->get();
        $totalCourses = $courses->count();
        $activeCourses = $courses->count(); // Все курсы считаем активными
        $totalStudentsOnCourses = $courses->sum(function($course) {
            return $course->groups->sum(function($group) {
                return $group->students->count();
            });
        });

        // Статистика пользователей
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Список преподавателей для модального окна
        $teachers = Teacher::with('user')->get();
        $admins = User::where('role', 'admin')->get();

        // Системная информация
        $systemInfo = [
            'version' => '1.0.0',
            'last_update' => now()->format('d.m.Y'),
            'status' => 'Активна'
        ];

        // Информация о резервных копиях
        $backupService = new BackupService();
        $backupsList = $backupService->getBackupsList();
        $latestBackup = !empty($backupsList) ? $backupsList[0] : null;
        
        $backupInfo = [
            'last_backup' => $latestBackup ? Carbon::parse($latestBackup['created_at'])->format('d.m.Y H:i') : 'Нет',
            'size' => $latestBackup ? $latestBackup['size'] : '0 B',
            'status' => $latestBackup ? 'Успешно' : 'Нет данных',
            'total_backups' => count($backupsList)
        ];

        // Системные логи (последние действия)
        $systemLogs = [
            [
                'timestamp' => now()->format('d.m.Y H:i'),
                'action' => 'Просмотр страницы управления',
                'user' => auth()->user()->name ?? 'Система'
            ],
            [
                'timestamp' => now()->subMinutes(30)->format('d.m.Y H:i'),
                'action' => 'Создана новая группа',
                'user' => 'Администратор'
            ],
            [
                'timestamp' => now()->subHours(2)->format('d.m.Y H:i'),
                'action' => 'Добавлен новый курс',
                'user' => 'Администратор'
            ]
        ];

        return view('admin/management', compact(
            'groupsList',
            'totalGroups',
            'totalStudentsInGroups',
            'totalCourses',
            'activeCourses',
            'totalStudentsOnCourses',
            'totalStudents',
            'totalTeachers',
            'totalAdmins',
            'systemInfo',
            'backupInfo',
            'systemLogs',
            'courses',
            'teachers',
            'admins'
        ));
    }

    // Методы для работы с группами
    public function createGroup(Request $request) {
        $request->validate([
            'name' => 'required|unique:groups,name',
            'teacher_id' => 'required|exists:teachers,id',
            'courses' => 'array'
        ]);

        $group = Group::create([
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'average_rating' => 0,
            'average_attendance' => 0,
            'average_exam' => 0,
            'size' => 0,
            'courses' => $request->courses ?? []
        ]);

        if ($request->courses) {
            $group->courses()->attach($request->courses);
        }

        // --- ЧАТЫ ---
        // 1. Создать групповой чат
        $chat = \App\Models\GroupChat::create([
            'group_id' => $group->id,
            'name' => 'Чат группы ' . $group->name
        ]);
        // 2. Добавить всех студентов группы
        $students = \App\Models\Student::where('group_name', $group->name)->get();
        foreach ($students as $student) {
            if ($student->users_id) {
                \App\Models\UserChat::create([
                    'group_chat_id' => $chat->id,
                    'user_id' => $student->users_id
                ]);
            }
        }
        // 3. Добавить преподавателя
        $teacher = \App\Models\Teacher::find($group->teacher_id);
        if ($teacher && $teacher->users_id) {
            \App\Models\UserChat::create([
                'group_chat_id' => $chat->id,
                'user_id' => $teacher->users_id
            ]);
        }
        // --- конец блок чатов ---

        return redirect()->back()->with('success', 'Группа успешно создана');
    }

    public function updateGroup(Request $request) {
        $request->validate([
            'group_id' => 'required|exists:groups,name',
            'name' => 'required|unique:groups,name,' . $request->group_id . ',name',
            'teacher_id' => 'required|exists:teachers,id',
            'courses' => 'array'
        ]);

        $group = Group::where('name', $request->group_id)->firstOrFail();
        $oldName = $group->name;
        $oldTeacherId = $group->teacher_id;
        $group->update([
            'name' => $request->name,
            'teacher_id' => $request->teacher_id,
            'courses' => $request->courses ?? [],
        ]);

        if ($request->courses) {
            $group->courses()->sync($request->courses);
        } else {
            $group->courses()->detach();
        }

        // --- ЧАТЫ: обновление преподавателя ---
        $chat = \App\Models\GroupChat::where('group_id', $group->id)->first();
        if ($chat) {
            // Если преподаватель изменился
            if ($oldTeacherId != $request->teacher_id) {
                // Удалить старого преподавателя из чата
                $oldTeacher = \App\Models\Teacher::find($oldTeacherId);
                if ($oldTeacher && $oldTeacher->users_id) {
                    \App\Models\UserChat::where('group_chat_id', $chat->id)
                        ->where('user_id', $oldTeacher->users_id)
                        ->delete();
                }
                // Добавить нового преподавателя в чат, если его нет
                $newTeacher = \App\Models\Teacher::find($request->teacher_id);
                if ($newTeacher && $newTeacher->users_id) {
                    $exists = \App\Models\UserChat::where('group_chat_id', $chat->id)
                        ->where('user_id', $newTeacher->users_id)
                        ->exists();
                    if (!$exists) {
                        \App\Models\UserChat::create([
                            'group_chat_id' => $chat->id,
                            'user_id' => $newTeacher->users_id
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', "Группа '{$oldName}' успешно обновлена");
    }

    public function deleteGroup(Request $request) {
        $group = Group::where('name', $request->group_id)->firstOrFail();
        $groupName = $group->name;
        $group->delete();

        return redirect()->back()->with('success', "Группа '{$groupName}' успешно удалена");
    }

    // AJAX метод для загрузки данных группы
    public function getGroupData(Request $request) {
        $group = Group::where('name', $request->group_name)->with('courses')->firstOrFail();
        $courses = $group->courses instanceof \Illuminate\Support\Collection ? $group->courses : collect();
        $courses_json = $group->courses;
        if (is_string($courses_json)) {
            $courses_json = json_decode($courses_json, true) ?? [];
        }
        if (!is_array($courses_json)) {
            $courses_json = [];
        }
        return response()->json([
            'name' => $group->name,
            'teacher_id' => $group->teacher_id,
            'courses' => $courses->pluck('id')->toArray(),
            'courses_json' => $courses_json,
        ]);
    }

    // Методы для работы с курсами
    public function createCourse(Request $request) {
        $request->validate([
            'name' => 'required|unique:courses,name',
            'access_groups' => 'array',
            'access_teachers' => 'array'
        ]);

        // Обрабатываем выбранных пользователей
        $selectedUsers = [];
        if ($request->access_teachers) {
            foreach ($request->access_teachers as $userValue) {
                if (strpos($userValue, 'teacher_') === 0) {
                    $teacherId = substr($userValue, 8); // Убираем 'teacher_'
                    $teacher = Teacher::find($teacherId);
                    if ($teacher && $teacher->user) {
                        $selectedUsers[] = $teacher->user->id; // id пользователя-учителя
                    }
                } elseif (strpos($userValue, 'admin_') === 0) {
                    $adminId = substr($userValue, 6); // Убираем 'admin_'
                    $admin = User::find($adminId);
                    if ($admin) {
                        $selectedUsers[] = $admin->id; // id пользователя-админа
                    }
                }
            }
        }

        Course::create([
            'name' => $request->name,
            'access_' => [
                'groups' => $request->access_groups ?? [],
                'teachers' => $selectedUsers
            ]
        ]);

        return redirect()->back()->with('success', 'Курс успешно создан');
    }

    public function updateCourse(Request $request) {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|unique:courses,name,' . $request->course_id,
            'access_groups' => 'array',
            'access_teachers' => 'array'
        ]);

        $course = Course::findOrFail($request->course_id);
        $oldName = $course->name;

        // Обрабатываем выбранных пользователей
        $selectedUsers = [];
        if ($request->access_teachers) {
            foreach ($request->access_teachers as $userValue) {
                if (strpos($userValue, 'teacher_') === 0) {
                    $teacherId = substr($userValue, 8);
                    $teacher = Teacher::find($teacherId);
                    if ($teacher && $teacher->user) {
                        $selectedUsers[] = $teacher->user->id;
                    }
                } elseif (strpos($userValue, 'admin_') === 0) {
                    $adminId = substr($userValue, 6);
                    $admin = User::find($adminId);
                    if ($admin) {
                        $selectedUsers[] = $admin->id;
                    }
                }
            }
        }

        $course->update([
            'name' => $request->name,
            'access_' => [
                'groups' => $request->access_groups ?? [],
                'teachers' => $selectedUsers
            ]
        ]);

        return redirect()->back()->with('success', "Курс '{$oldName}' успешно обновлен");
    }

    public function deleteCourse(Request $request) {
        $course = Course::findOrFail($request->course_id);
        $courseName = $course->name;
        $course->delete();

        return redirect()->back()->with('success', "Курс '{$courseName}' успешно удален");
    }

    // Метод для создания резервной копии
    public function createBackup() {
        $backupService = new BackupService();
        $result = $backupService->createBackup();
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Резервная копия создана успешно',
                'backup_name' => $result['backup_name'],
                'size' => $result['size']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании резервной копии: ' . $result['error']
            ], 500);
        }
    }

    // Метод для восстановления из резервной копии
    public function restoreBackup(Request $request) {
        $request->validate([
            'backup_name' => 'required|string'
        ]);

        $backupService = new BackupService();
        $result = $backupService->restoreBackup($request->backup_name);
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при восстановлении: ' . $result['error']
            ], 500);
        }
    }

    // Метод для получения списка резервных копий
    public function getBackupsList() {
        $backupService = new BackupService();
        $backups = $backupService->getBackupsList();
        
        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    public function deleteBackup(Request $request) {
        $request->validate([
            'backup_name' => 'required|string'
        ]);
        $backupService = new \App\Services\BackupService();
        $result = $backupService->deleteBackup($request->backup_name);
        if ($result['success']) {
            return response()->json(['success' => true, 'message' => 'Бэкап успешно удалён']);
        } else {
            return response()->json(['success' => false, 'message' => $result['message'] ?? 'Ошибка удаления бэкапа'], 500);
        }
    }

    public function getCourseData(Request $request) {
        $course = \App\Models\Course::findOrFail($request->course_id);
        $access = is_array($course->access_) ? $course->access_ : json_decode($course->access_, true);
        
        // Преобразуем id пользователей в формат teacher_{id} и admin_{id}
        $formattedTeachers = [];
        if (isset($access['teachers']) && is_array($access['teachers'])) {
            foreach ($access['teachers'] as $userId) {
                // Проверяем, является ли пользователь преподавателем
                $teacher = \App\Models\Teacher::where('users_id', $userId)->first();
                if ($teacher) {
                    $formattedTeachers[] = 'teacher_' . $teacher->id;
                } else {
                    // Если не преподаватель, значит админ
                    $formattedTeachers[] = 'admin_' . $userId;
                }
            }
        }
        
        return response()->json([
            'name' => $course->name,
            'groups' => $access['groups'] ?? [],
            'teachers' => $formattedTeachers,
        ]);
    }

    public function getAutoBackupSettings() {
        $path = storage_path('app/auto_backup_settings.json');
        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            return response()->json([
                'enabled' => $data['enabled'] ?? false,
                'period' => $data['period'] ?? 'daily',
                'time' => $data['time'] ?? '03:00',
            ]);
        } else {
            return response()->json([
                'enabled' => false,
                'period' => 'daily',
                'time' => '03:00',
            ]);
        }
    }

    public function saveAutoBackupSettings(Request $request) {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'period' => 'required|in:daily,weekly,monthly',
            'time' => 'required|regex:/^\d{2}:\d{2}$/',
        ]);
        $path = storage_path('app/auto_backup_settings.json');
        file_put_contents($path, json_encode($validated, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        return response()->json(['success' => true]);
    }
}
