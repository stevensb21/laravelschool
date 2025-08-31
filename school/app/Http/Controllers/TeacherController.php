<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Review;
use App\Models\GroupChat;
use App\Models\UserChat;

class TeacherController extends Controller
{

    public function edit(Request $request){
        try {
            DB::beginTransaction();
            
            // Валидация данных
            $validated = $request->validate([
                'users_id' => 'required|exists:users,id',
                'name' => 'required|unique:users,name,' . $request->users_id,
                'fio' => 'required',
                'job_title' => 'required',
                'email' => 'required|email',
                'subjects' => 'required',
                'education' => 'required',
                'achievements' => 'nullable'
            ], [
                'users_id.required' => 'ID пользователя обязателен',
                'users_id.exists' => 'Пользователь не найден',
                'name.required' => 'Логин обязателен для заполнения',
                'name.unique' => 'Пользователь с таким логином уже существует',
                'fio.required' => 'ФИО обязательно для заполнения',
                'job_title.required' => 'Должность обязательна для заполнения',
                'email.required' => 'Email обязателен для заполнения',
                'email.email' => 'Введите корректный email',
                'subjects.required' => 'Предметы обязательны для заполнения',
                'education.required' => 'Образование обязательно для заполнения',
            ]);

            $teacher = Teacher::where('users_id', $validated['users_id'])->first();

            if (!$teacher) {
                throw new \Exception('Преподаватель не найден');
            }

            // Находим пользователя
            $user = User::find($validated['users_id']);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            // Обновляем пользователя
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            // Обновляем пароль, если он указан
            if ($request->filled('password')) {
                $user->update([
                    'password' => bcrypt($request->password)
                ]);
            }

            // Преобразуем текстовые поля в массивы
            $subjects = array_filter(explode("\n", str_replace("\r", "", $validated['subjects'])));
            $education = array_filter(explode("\n", str_replace("\r", "", $validated['education'])));
            $achievements = $validated['achievements'] ? array_filter(explode("\n", str_replace("\r", "", $validated['achievements']))) : [];

            \Log::info('Education from request:', [$validated['education']]);
            \Log::info('Education parsed:', [$education]);

            // Обновляем преподавателя
            $teacher->update([
                'fio' => $validated['fio'],
                'job_title' => $validated['job_title'],
                'subjects' => $subjects,
                'education' => $education,
                'achievements' => $achievements,
                'email' => $validated['email'],
            ]);

            \Log::info('Teacher after update:', [$teacher->fresh()]);

            DB::commit();
            return redirect()->back()->with('success', 'Преподаватель успешно отредактирован');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при редактировании преподавателя', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Произошла ошибка при редактировании преподавателя: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Request $request){
        $userId = $request->input('users_id');

        DB::beginTransaction();
        try {
            // Удаляем преподавателя по users_id
            Teacher::where('users_id', $userId)->delete();

            // Удаляем пользователя по id
            User::where('id', $userId)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Преподаватель и пользователь удалены');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при удалении: ' . $e->getMessage());
        }
    }

    public function index(Request $request) {
        $allSubjects = Course::pluck('name')->toArray();

        $query = Teacher::query();

        // Поиск по ФИО
        if ($request->filled('fio')) {
            $query->where('fio', 'LIKE', '%' . $request->fio . '%');
        }
        // if ($request->filled('subjects')) {
        //     // Предположим, что предметы хранятся в колонке 'subject' (строка)
        //     $query->where('subject', $request->subject);
        // }
        // Поиск по предмету
        if ($request->filled('subject')) {
            $query->where(function($q) use ($request) {
                $q->where('subjects', 'LIKE', '%' . $request->subject . '%')
                ->orWhere('subjects', 'LIKE', $request->subject . ',%')
                ->orWhere('subjects', 'LIKE', '%,' . $request->subject . ',%')
                ->orWhere('subjects', 'LIKE', '%,' . $request->subject);
            });
        }
        
        $teachers = $query->get();
        // Пересчитываем статистику для каждого преподавателя через getStatistics()
        foreach ($teachers as $teacher) {
            if (method_exists($teacher, 'getStatistics')) {
                $stats = $teacher->getStatistics();
                $teacher->average_attendance = $stats['average_attendance'] ?? 0;
                $teacher->average_exam_score = $stats['average_homework'] ?? ($stats['average_exam_score'] ?? 0);
                $teacher->average_performance = $stats['average_performance'] ?? 0;
            }
        }
        
        // Получаем уникальные предметы для выпадающего списка
        

        
        return view('admin/teachers', compact('teachers', 'allSubjects'));
    }

  
    public function store(Request $request)
    {
        \Log::info('=== НАЧАЛО СОЗДАНИЯ ПРЕПОДАВАТЕЛЯ ===');
        \Log::info('Полученные данные:', $request->all());
        
        try {
            DB::beginTransaction();
            
            \Log::info('Начало создания преподавателя', ['request_data' => $request->all()]);
            
            // Валидация данных
            $validated = $request->validate([
                'name' => 'required|unique:users,name',
                'password' => 'required',
                'fio' => 'required',
                'job_title' => 'required',
                'email' => 'required|email',
                'subjects' => 'required',
                'education' => 'required',
                'achievements' => 'nullable'
            ], [
                'name.required' => 'Логин обязателен для заполнения',
                'name.unique' => 'Пользователь с таким логином уже существует',
                'password.required' => 'Пароль обязателен для заполнения',
                'fio.required' => 'ФИО обязательно для заполнения',
                'job_title.required' => 'Должность обязательна для заполнения',
                'email.required' => 'Email обязателен для заполнения',
                'email.email' => 'Введите корректный email',
                'subjects.required' => 'Предметы обязательны для заполнения',
                'education.required' => 'Образование обязательно для заполнения',
            ]);

            \Log::info('Данные прошли валидацию', ['validated_data' => $validated]);

            // Создаем пользователя
            $user = User::create([
                'name' => $validated['name'],
                'password' => bcrypt($validated['password']),
                'email' => $validated['email'],
                'role' => 'teacher',
                
            ]);

            \Log::info('Пользователь создан', ['user_id' => $user->id]);

            if (!$user) {
                throw new \Exception('Не удалось создать пользователя');
            }

            // Преобразуем текстовые поля в массивы
            $subjects = array_filter(explode("\n", str_replace("\r", "", $validated['subjects'])));
            $education = array_filter(explode("\n", str_replace("\r", "", $validated['education'])));
            $achievements = $validated['achievements'] ? array_filter(explode("\n", str_replace("\r", "", $validated['achievements']))) : [];

            \Log::info('Подготовлены данные для создания преподавателя', [
                'subjects' => $subjects,
                'education' => $education,
                'achievements' => $achievements
            ]);

            // Создаем преподавателя
            $teacher = Teacher::create([
                'users_id' => $user->id,
                'fio' => $validated['fio'],
                'job_title' => $validated['job_title'],
                'subjects' => $subjects,
                'education' => $education,
                'achievements' => $achievements,
                'email' => $validated['email'],
                'average_performance' => 0,
                'average_attendance' => 0,
                'average_exam_score' => 0
            ]);

            \Log::info('Преподаватель создан', ['teacher_id' => $teacher->id]);

            if (!$teacher) {
                throw new \Exception('Не удалось создать преподавателя');
            }

            // Добавляем преподавателя в чат преподавателей
            try {
                $teachersChat = GroupChat::where('name', 'Чат с преподавателями')->first();
                if ($teachersChat) {
                    UserChat::firstOrCreate([
                        'group_chat_id' => $teachersChat->id,
                        'user_id' => $user->id
                    ]);
                    \Log::info('Преподаватель добавлен в чат', [
                        'teacher_id' => $teacher->id,
                        'user_id' => $user->id,
                        'chat_id' => $teachersChat->id
                    ]);
                } else {
                    \Log::warning('Чат с преподавателями не найден, преподаватель не добавлен в чат');
                }
            } catch (\Exception $e) {
                \Log::error('Ошибка при добавлении преподавателя в чат', [
                    'teacher_id' => $teacher->id,
                    'error' => $e->getMessage()
                ]);
                // Не прерываем создание преподавателя из-за ошибки добавления в чат
            }

            DB::commit();
            \Log::info('Транзакция успешно завершена');
            return redirect()->back()->with('success', 'Преподаватель успешно добавлен');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при создании преподавателя', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Произошла ошибка при добавлении преподавателя: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Получает преподавателя с учетом параметра teacher_id от администратора
     */
    private function getTeacherForAdmin($request = null) {
        $user = auth()->user();
        
        // Если есть параметр teacher_id и пользователь - администратор, получаем указанного преподавателя
        if ($request && $request->filled('teacher_id') && $user->role === 'admin') {
            $teacherId = $request->input('teacher_id');
            return Teacher::where('users_id', $teacherId)->with('user')->first();
        }
        
        // Иначе возвращаем преподавателя текущего пользователя
        return $user->teacher;
    }

    public function account(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        // Вычисляем статистику динамически с обработкой ошибок
        try {
            $statistics = $teacher->getStatistics();
        } catch (\Exception $e) {
            \Log::error('Ошибка при вычислении статистики преподавателя', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            
            // Возвращаем значения по умолчанию в случае ошибки
            $statistics = [
                'average_performance' => 0,
                'average_attendance' => 0,
                'average_exam_score' => 0
            ];
        }
        
        // Получаем отзывы о преподавателе (только одобренные)
        $reviews = \App\Models\Review::where('recipient_type', 'teacher')
            ->where('recipient_id', $teacher->id)
            ->where('status', 'approved')
            ->with(['sender'])
            ->latest()
            ->get();
            
        // Добавляем имена отправителей
        $reviews->each(function($review) {
            if ($review->sender_type === 'teacher') {
                $sender = Teacher::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
            } else {
                $sender = Student::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Студент';
            }
        });
        
        // Определяем, является ли текущий пользователь администратором
        $isAdmin = auth()->user()->role === 'admin';
        
        return view('teacher.account', compact('teacher', 'statistics', 'isAdmin', 'reviews'));
    }

    /**
     * Получает статистику преподавателя без обновления базы данных
     */
    public function getStatistics()
    {
        $user = auth()->user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return response()->json(['error' => 'Преподаватель не найден'], 404);
        }
        
        try {
            $statistics = $teacher->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            \Log::error('Ошибка при получении статистики преподавателя', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Ошибка при вычислении статистики',
                'data' => [
                    'average_performance' => 0,
                    'average_attendance' => 0,
                    'average_exam_score' => 0
                ]
            ], 500);
        }
    }

    public function students(Request $request) {
        $teacher = $this->getTeacherForAdmin($request);

        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }

        $teacherId = $teacher->id; // Используем ID преподавателя из таблицы teachers
        
        // Получаем все группы, где этот преподаватель записан в teacher_id
        $groups = \App\Models\Group::where('teacher_id', $teacherId)->pluck('name');
        
        $allGroups = $groups;

        // Если у преподавателя нет групп - показываем пусто
        if ($groups->isEmpty()) {
            $students = collect();
            $isAdmin = auth()->user()->role === 'admin';
            return view('teacher.students', compact('students', 'teacher', 'allGroups', 'isAdmin'));
        }

        $studentsQuery = \App\Models\Student::whereIn('group_name', $groups)->with('user', 'group');
        if ($request->filled('group')) {
            $studentsQuery->where('group_name', $request->input('group'));
        }
        $students = $studentsQuery->get();

        // === Новый расчёт статистики для каждого студента ===
        foreach ($students as $student) {
            // 1. Собираем все lesson statistics для этого студента, где lesson ведёт этот преподаватель
            $lessonStats = collect();
            $lessonIds = [];
            foreach ($student->statistics as $stat) {
                if (preg_match('/lesson:(\d+)/', $stat->notes, $m)) {
                    $lessonId = $m[1];
                    $calendar = \App\Models\Calendar::find($lessonId);
                    if ($calendar && str_replace(' ', '', mb_strtolower($calendar->teacher)) == str_replace(' ', '', mb_strtolower($teacher->fio))) {
                        $lessonStats->push($stat);
                        $lessonIds[] = $lessonId;
                    }
                }
            }
            // Средний балл за уроки
            $student->average_performance = $lessonStats->where('grade_lesson', '>', 0)->avg('grade_lesson') ? round($lessonStats->where('grade_lesson', '>', 0)->avg('grade_lesson'), 1) : 0;
            // Посещаемость
            $totalLessons = $lessonStats->count();
            $attendedLessons = $lessonStats->where('attendance', true)->count();
            $student->average_attendance = $totalLessons > 0 ? round($attendedLessons / $totalLessons * 100, 1) : 0;
            // Средний балл за домашки, которые задал этот преподаватель
            $homeworkStats = \App\Models\HomeWorkStudent::where('student_id', $student->id)
                ->where('grade', '>', 0)
                ->get()
                ->filter(function($hws) use ($teacher) {
                    return $hws->homework && $hws->homework->teacher && $hws->homework->teacher->users_id == $teacher->users_id;
                });
            $student->average_homework = $homeworkStats->count() > 0 ? round($homeworkStats->avg('grade'), 1) : 0;
        }
        // === Конец нового расчёта ===

        $isAdmin = auth()->user()->role === 'admin';
        return view('teacher.students', compact('students', 'teacher', 'allGroups', 'isAdmin'));
    }

    public function lesson(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        // Получаем уроки на сегодня для этого преподавателя
        $today = date('Y-m-d');
        $lessons = \App\Models\Calendar::where('teacher', $teacher->fio)
            ->where('date_', $today)
            ->orderBy('start_time')
            ->get();
        
        $isAdmin = auth()->user()->role === 'admin';
        return view('teacher.lesson', compact('teacher', 'lessons', 'isAdmin'));
    }

    public function methodology(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        // Получаем курсы, которые ведет преподаватель
        $courses = \App\Models\Course::where(function($q) use ($teacher) {
            $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
              ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
        })->get();
        
        // Получаем методпакеты для курсов, которые ведет преподаватель
        $query = \App\Models\Method::whereHas('course', function($query) use ($teacher) {
            $query->whereJsonContains('access_->teachers', $teacher->users_id);
        })->with('course');
        
        // Применяем фильтр по курсу
        if (request('course')) {
            $query->whereHas('course', function($q) {
                $q->where('name', request('course'));
            });
        }
        
        $methods = $query->get();
        
        $isAdmin = auth()->user()->role === 'admin';
        return view('teacher.methodology', compact('methods', 'teacher', 'courses', 'isAdmin'));
    }

    public function homework(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        // Получаем курсы, которые ведет преподаватель
        $courses = \App\Models\Course::where(function($q) use ($teacher) {
            $q->whereJsonContains('access_->teachers', (int)$teacher->users_id)
              ->orWhereJsonContains('access_->teachers', (string)$teacher->users_id);
        })->get();
        
        // Получаем группы, где преподает этот преподаватель
        $courseIds = $courses->pluck('id')->toArray();
        $groups = \App\Models\Group::where(function($q) use ($courseIds) {
            foreach ($courseIds as $cid) {
                $q->orWhereJsonContains('courses', (int)$cid)
                  ->orWhereJsonContains('courses', (string)$cid);
            }
        })->get();
        
        // Получаем домашние задания, созданные этим преподавателем
        $query = \App\Models\HomeWork::where('teachers_id', $teacher->id)
            ->with(['group', 'course', 'method', 'homeWorkStudents']);
        
        // Применяем фильтры
        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }
        
        if (request('course')) {
            $query->where('course_id', request('course'));
        }
        
        if (request('group')) {
            $query->where('groups_id', request('group'));
        }
        
        if (request('status')) {
            $query->where('status', request('status'));
        }
        
        $homeworks = $query->orderBy('created_at', 'desc')->get();
        
        // Принудительно обновляем статус всех домашних заданий
        foreach ($homeworks as $homework) {
            $homework->updateStatusIfNeeded();
            $homework->saveQuietly();
        }
        
        $isAdmin = auth()->user()->role === 'admin';
        return view('teacher.homework', compact('homeworks', 'teacher', 'courses', 'groups', 'isAdmin'));
    }

    public function appeals(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        $user = $teacher->user;
        
        // Получаем администраторов для выпадающего списка
        $admins = \App\Models\User::where('role', 'admin')->get();
        
        // Получаем студентов из групп, где этот преподаватель записан в teacher_id
        $groups = \App\Models\Group::where('teacher_id', $teacher->id)->pluck('name');
        $students = collect();
        
        if (!$groups->isEmpty()) {
            $students = \App\Models\Student::whereIn('group_name', $groups)->with('user')->get();
        }
        
        // Получаем все обращения где преподаватель отправитель или получатель
        $appeals = \App\Models\Appeal::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['sender.student', 'sender.teacher', 'recipient.student', 'recipient.teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        $sentAppeals = $appeals->where('sender_id', $user->id);
        $receivedAppeals = $appeals->where('recipient_id', $user->id);
        
        $isAdmin = auth()->user()->role === 'admin';
        return view('teacher.appeals', compact('appeals', 'teacher', 'sentAppeals', 'receivedAppeals', 'admins', 'students', 'isAdmin'));
    }

    public function sendAppeal(Request $request) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        $user = $teacher->user;
        
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'comment' => 'required|string|max:2000',
        ]);
        
        \App\Models\Appeal::create([
            'sender_id' => $user->id,
            'recipient_id' => $request->recipient_id,
            'title' => $request->subject,
            'description' => $request->comment,
            'type' => 'Вопрос', // По умолчанию тип "Вопрос"
            'status' => 'Активно',
        ]);
        
        // Если это администратор, добавляем параметр teacher_id к редиректу
        if ($request->filled('teacher_id') && auth()->user()->role === 'admin') {
            return redirect()->route('teacher.appeals', ['teacher_id' => $request->input('teacher_id')])->with('success', 'Обращение отправлено!');
        }
        
        return redirect()->route('teacher.appeals')->with('success', 'Обращение отправлено!');
    }

    public function replyToAppeal(Request $request, $id) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Преподаватель не найден'
            ], 404);
        }
        
        $user = $teacher->user;
        $appeal = \App\Models\Appeal::findOrFail($id);
        
        // Проверяем, что пользователь является получателем обращения
        if ($appeal->recipient_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для ответа на это обращение'
            ], 403);
        }
        
        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);
        
        $appeal->update([
            'feedback' => $request->feedback,
            'status' => 'Завершено'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ответ успешно добавлен',
            'appeal' => $appeal->load(['sender', 'recipient'])
        ]);
    }

    public function calendar(Request $request = null) {
        $teacher = $this->getTeacherForAdmin($request);
        
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        
        // Инициализируем даты недели
        if (!session()->has(['monday', 'sunday'])) {
            $date = now();
            session()->put([
                'monday' => date('Y-m-d', strtotime('monday this week')),
                'sunday' => date('Y-m-d', strtotime('sunday this week'))
            ]);
        }
        
        // Получаем группы, где этот преподаватель записан в teacher_id
        $groups = \App\Models\Group::where('teacher_id', $teacher->id)->get();
        
        // Получаем предметы из курсов этих групп
        $courseIds = [];
        foreach ($groups as $group) {
            if ($group->courses) {
                $groupCourses = is_array($group->courses) ? $group->courses : json_decode($group->courses, true);
                if (is_array($groupCourses)) {
                    $courseIds = array_merge($courseIds, $groupCourses);
                }
            }
        }
        $courseIds = array_unique($courseIds);
        
        $subjects = \App\Models\Course::whereIn('id', $courseIds)->pluck('name')->toArray();
        
        // Для отображения: ВСЕ уроки этого преподавателя за неделю
        $allLessonsQuery = \App\Models\Calendar::whereBetween('date_', [session('monday'), session('sunday')])
            ->where('teacher', $teacher->fio);
        $lessons = $allLessonsQuery->get();
        
        // Применяем фильтры только если выбраны
        if (request('group')) {
            $lessons = $lessons->where('name_group', request('group'));
        } else {
            // не фильтруем по группам, показываем все свои уроки
        }
        if (request('subject')) {
            $lessons = $lessons->where('subject', request('subject'));
        } else {
            // не фильтруем по предметам, показываем все свои уроки
        }
        $schedule = $this->buildSchedule($lessons);
        $data = [
            'lessons' => $lessons,
            'groups' => $groups,
            'subjects' => $subjects,
            'teachers' => [$teacher],
            'user' => $teacher,
            'schedule' => $schedule,
            'selectedGroup' => request('group', ''),
            'selectedSubject' => request('subject', ''),
            'selectedTeacher' => $teacher->fio,
            'isAdmin' => auth()->user()->role === 'admin',
            'isTeacher' => true,
            'isStudent' => false,
            'edit_mode' => request('edit_mode'),
        ];
        return view('teacher.calendar', compact('data'));
    }
    
    private function buildSchedule($lessons) {
        $schedule = array_fill(1, 7, array_fill(8, 22, null));
        foreach ($lessons as $lesson) {
            // Преобразуем урок в массив, если это объект
            $lessonArray = is_array($lesson) ? $lesson : $lesson->toArray();
            
            $day = date('w', strtotime($lessonArray['date_']));
            if($day == 0) {
                $day = 7;
            }
            
            // Получаем время начала и окончания
            $start_timestamp = strtotime($lessonArray['start_time']);
            $end_timestamp = strtotime($lessonArray['end_time']);
            
            $start_hour = (int)date('G', $start_timestamp);
            $start_minute = (int)date('i', $start_timestamp);
            $end_hour = (int)date('G', $end_timestamp);
            $end_minute = (int)date('i', $end_timestamp);
            
            // Заполняем массив расписания
            for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
                if ($hour == $start_hour) {
                    $schedule[$day][$hour] = $lessonArray;
                } 
                elseif ($hour == $end_hour) {
                    if ($end_minute == 0 && $hour > $start_hour) {
                        continue;
                    }
                    $schedule[$day][$hour] = $lessonArray;
                } 
                else {
                    $schedule[$day][$hour] = $lessonArray;
                }
            }
        }
        return $schedule;
    }
    
    public function calendarPrevWeek(Request $request = null) {
        session()->put('monday', date('Y-m-d', strtotime(session('monday') . ' -7 days')));
        session()->put('sunday', date('Y-m-d', strtotime(session('sunday') . ' -7 days')));
        
        // Если это администратор, добавляем параметр teacher_id к редиректу
        if ($request && $request->filled('teacher_id') && auth()->user()->role === 'admin') {
            return redirect()->route('teacher.calendar', ['teacher_id' => $request->input('teacher_id')]);
        }
        
        return redirect()->route('teacher.calendar');
    }

    public function calendarNextWeek(Request $request = null) {
        session()->put('monday', date('Y-m-d', strtotime(session('monday') . ' +7 days')));
        session()->put('sunday', date('Y-m-d', strtotime(session('sunday') . ' +7 days')));
        
        // Если это администратор, добавляем параметр teacher_id к редиректу
        if ($request && $request->filled('teacher_id') && auth()->user()->role === 'admin') {
            return redirect()->route('teacher.calendar', ['teacher_id' => $request->input('teacher_id')]);
        }
        
        return redirect()->route('teacher.calendar');
    }

    public function studentProfile($id) {
        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        $student = \App\Models\Student::with('user', 'group')->findOrFail($id);
        // Получаем отзывы о студенте (только одобренные)
        $reviews = \App\Models\Review::where('recipient_type', 'student')
            ->where('recipient_id', $student->id)
            ->where('status', 'approved')
            ->with(['sender'])
            ->latest()
            ->get();
        // Добавляем имена отправителей
        $reviews->each(function($review) {
            if ($review->sender_type === 'teacher') {
                $sender = \App\Models\Teacher::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
            } else {
                $sender = \App\Models\Student::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Студент';
            }
        });
        return view('teacher.student-profile', compact('student', 'teacher', 'reviews'));
    }

    public function lessonStudents($lesson_id) {
        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        $lesson = \App\Models\Calendar::findOrFail($lesson_id);
        $groupName = $lesson->name_group;
        $students = \App\Models\Student::where('group_name', $groupName)->get();

        // Получаем методпакет для курса этого урока
        $method = null;
        $homeworkTitles = [];
        $homeworkFiles = [];
        $course = null;
        $currentHomework = null;
        if ($lesson->subject) {
            $course = \App\Models\Course::where('name', $lesson->subject)->first();
            if ($course) {
                $method = \App\Models\Method::where('course_id', $course->id)->first();
                if ($method) {
                    $homeworkTitles = $method->title_homework ?? [];
                    $homeworkFiles = $method->homework ?? [];
                }
            }
        }
        // Текущее домашнее задание для этой группы, предмета и учителя
        if ($students->count() && $course) {
            $currentHomework = \App\Models\HomeWork::where('groups_id', $students->first()->group->id ?? null)
                ->where('course_id', $course->id)
                ->where('teachers_id', $teacher->id)
                ->orderByDesc('created_at')
                ->first();
        }
        $studentGrades = [];
        $studentAttendance = [];
        foreach ($students as $student) {
            $stat = $student->statistics()
                ->where('notes', 'like', 'lesson:' . $lesson->id . ';%')
                ->orderByDesc('created_at')
                ->first();
            $studentGrades[$student->id] = $stat ? $stat->grade_lesson : '';
            $studentAttendance[$student->id] = $stat ? $stat->attendance : false;
        }
        return view('teacher.lesson-students', compact('teacher', 'lesson', 'students', 'homeworkTitles', 'homeworkFiles', 'currentHomework', 'studentGrades', 'studentAttendance'));
    }

    public function saveLessonAttendance($lesson_id, \Illuminate\Http\Request $request) {
        $user = auth()->user();
        $teacher = $user->teacher;
        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Преподаватель не найден');
        }
        $lesson = \App\Models\Calendar::findOrFail($lesson_id);
        $groupName = $lesson->name_group;
        $students = \App\Models\Student::where('group_name', $groupName)->get();

        $attendance = $request->input('attendance', []);
        $grades = $request->input('grade', []);

        // --- ГАРАНТИРУЕМ methodId определён ---
        $methodId = 0;

        // --- Сохраняем посещаемость и оценки ---
        foreach ($students as $student) {
            $wasPresent = isset($attendance[$student->id]) && $attendance[$student->id] == 1;
            $gradeRaw = $grades[$student->id] ?? null;
            $grade = isset($grades[$student->id]) && $grades[$student->id] !== '' ? floatval($grades[$student->id]) : null;
            
            \Log::info('grade debug', [
                'student_id' => $student->id,
                'grade_raw' => $gradeRaw,
                'grade_final' => $grade,
                'all_grades' => $grades,
                'wasPresent' => $wasPresent,
                'notes_key' => 'lesson:' . $lesson->id . ';date:' . $lesson->date_ . ';group:' . $lesson->name_group,
            ]);

            \App\Models\Statistic::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'notes' => 'lesson:' . $lesson->id . ';date:' . $lesson->date_ . ';group:' . $lesson->name_group,
                ],
                [
                    'grade_lesson' => $grade,
                    'homework' => 0,
                    'attendance' => $wasPresent,
                    'notes' => 'lesson:' . $lesson->id . ';date:' . $lesson->date_ . ';group:' . $lesson->name_group,
                ]
            );
        }

        // --- Сохраняем домашнее задание ---
        $homeworkFromMethod = $request->input('homework_from_method');
        $homeworkFile = $request->file('homework_file');
        $homeworkFilePath = null;
        $courseId = null;
        $description = null;

        // Логирование для диагностики загрузки файла
        \Log::info('Файл для домашки', [
            'exists' => $request->hasFile('homework_file'),
            'valid' => $homeworkFile ? $homeworkFile->isValid() : null,
            'size' => $homeworkFile ? $homeworkFile->getSize() : null,
            'original_name' => $homeworkFile ? $homeworkFile->getClientOriginalName() : null,
        ]);

        // Определяем course_id и method_id
        $method = null;
        $methodId = null; // инициализируем как null
        $groupId = $students->first()->group->id ?? null;
        if ($lesson->subject) {
            $course = \App\Models\Course::where('name', $lesson->subject)->first();
            if ($course) {
                $courseId = $course->id;
                $method = \App\Models\Method::where('course_id', $course->id)->first();
                if ($method) {
                    $methodId = $method->id;
                }
            }
        }
        
        // Логируем для диагностики
        \Log::info('Определение method_id', [
            'lesson_subject' => $lesson->subject,
            'course_id' => $courseId ?? null,
            'method_id' => $methodId,
            'method_found' => $method ? true : false
        ]);

        // Проверяем, что файл действительно загружен и не пустой
        if ($homeworkFile && $homeworkFile->isValid() && $homeworkFile->getSize() > 0) {
            // Загружаем файл
            $homeworkFilePath = $homeworkFile->store('homework_teacher', 'public');
            $description = 'Загружено преподавателем';
            $methodId = null; // null для файла преподавателя
        } elseif ($homeworkFromMethod && !empty($homeworkFromMethod) && $methodId) {
            // Удаляем storage/ или /storage/ из начала пути, если есть
            if (strpos($homeworkFromMethod, '/storage/') === 0) {
                $homeworkFilePath = substr($homeworkFromMethod, 9);
            } elseif (strpos($homeworkFromMethod, 'storage/') === 0) {
                $homeworkFilePath = substr($homeworkFromMethod, 8);
            } else {
                $homeworkFilePath = $homeworkFromMethod;
            }
            $description = 'Из методпакета';
            // methodId уже определён выше
        } elseif ($homeworkFromMethod && !empty($homeworkFromMethod) && !$methodId) {
            if (strpos($homeworkFromMethod, '/storage/') === 0) {
                $homeworkFilePath = substr($homeworkFromMethod, 9);
            } elseif (strpos($homeworkFromMethod, 'storage/') === 0) {
                $homeworkFilePath = substr($homeworkFromMethod, 8);
            } else {
                $homeworkFilePath = $homeworkFromMethod;
            }
            $description = 'Из методпакета';
            $methodId = null;
        }

        // Проверка: если нет курса или группы — не сохраняем домашку
        if ($homeworkFilePath) {
            if (!$courseId || !$groupId) {
                return redirect()->back()->with('error', 'Ошибка: не найден курс или группа для домашнего задания.');
            }
            $data = [
                'name' => $lesson->subject . ' ' . $lesson->name_group,
                'groups_id' => $groupId,
                'course_id' => $courseId,
                'method_id' => $methodId, // используем найденный method_id или null
                'teachers_id' => $teacher->id,
                'deadline' => now()->addDays(7)->toDateString(),
                'file_path' => $homeworkFilePath,
                'description' => $description,
                'status' => 'Активно',
            ];
            \Log::info('Создание/обновление домашки', $data);
            $existing = \App\Models\HomeWork::where('groups_id', $groupId)
                ->where('course_id', $courseId)
                ->whereDate('deadline', $lesson->date_)
                ->first();
            if ($existing) {
                $existing->update($data);
            } else {
                \App\Models\HomeWork::create($data);
            }
        }

        return redirect()->back()->with('success', 'Посещаемость, оценки и домашнее задание сохранены!');
    }

    /**
     * Профиль преподавателя для администратора
     */
    public function adminProfile($id)
    {
        $teacher = Teacher::where('users_id', $id)->with('user')->firstOrFail();
        // Получаем отзывы о преподавателе (только одобренные)
        $teacherReviews = \App\Models\Review::where('recipient_type', 'teacher')
            ->where('recipient_id', $teacher->id)
            ->where('status', 'approved')
            ->with(['sender'])
            ->latest()
            ->get();
        // Добавляем имена отправителей
        $teacherReviews->each(function($review) {
            if ($review->sender_type === 'teacher') {
                $sender = \App\Models\Teacher::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Преподаватель';
            } else {
                $sender = \App\Models\Student::where('users_id', $review->sender_id)->first();
                $review->sender_name = $sender ? $sender->fio : 'Студент';
            }
        });
        // Получаем статистику динамически
        $statistics = $teacher->getStatistics();
        $average_rating = $teacher->average_rating ?? 0;
        return view('admin.teacher-profile', compact('teacher', 'teacherReviews', 'statistics', 'average_rating'));
    }
}



