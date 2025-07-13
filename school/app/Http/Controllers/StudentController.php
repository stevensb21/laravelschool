<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class StudentController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        if ($user->role !== 'admin') {
            return redirect()->route('student.account');
        }
        $allGroups = Group::pluck('name')->toArray();

        $query = Student::query();

        // Поиск по ФИО
        if ($request->filled('fio')) {
            $query->where('fio', 'LIKE', '%' . $request->fio . '%');
        }
        // if ($request->filled('group')) {
        //     // Предположим, что предметы хранятся в колонке 'subject' (строка)
        //     $query->where('group_name', $request->subject);
        // }
        // Поиск по предмету
        if ($request->filled('group')) {
            $query->where(function($q) use ($request) {
                $q->where('group_name', 'LIKE', '%' . $request->group . '%')
                ->orWhere('group_name', 'LIKE', $request->group . ',%')
                ->orWhere('group_name', 'LIKE', '%,' . $request->group . ',%')
                ->orWhere('group_name', 'LIKE', '%,' . $request->group);
            });
        }
        
        $students = $query->with('user')->get();
        
        // Фильтруем студентов, у которых есть связанный пользователь
        $students = $students->filter(function($student) {
            return $student->user !== null;
        });
        
        // Получаем уникальные предметы для выпадающего списка
        

        //dd($allSubjects);
        
        return view("admin/students",compact("students", 'allGroups'));
    }

    public function edit(Request $request) {
        try {
            DB::beginTransaction();
            
            \Log::info('Начало редактирования студента', ['request_data' => $request->all()]);
            
            // Валидация данных
            $validated = $request->validate([
                'users_id' => 'required|exists:users,id',
                'login' => 'required|unique:users,name,' . $request->users_id,
                'fio' => 'required',
                'numberphone' => 'required',
                'email' => 'required|email|unique:students,email,' . $request->users_id . ',users_id',
                'femaleparent' => 'required',
                'numberparent' => 'required',
                'group' => 'required',
                'datebirthday' => 'required',
                'achievements' => 'nullable',
            ], [
                'users_id.required' => 'ID пользователя обязателен',
                'users_id.exists' => 'Пользователь не найден',
                'login.required' => 'Логин обязателен для заполнения',
                'login.unique' => 'Пользователь с таким логином уже существует',
                'fio.required' => 'ФИО обязательно для заполнения',
                'numberphone.required' => 'Номер телефона обязателен для заполнения',
                'email.required' => 'Email обязателен для заполнения',
                'email.email' => 'Введите корректный email',
                'email.unique' => 'Студент с таким email уже существует',
                'femaleparent.required' => 'ФИО родителя обязательно для заполнения',
                'numberparent.required' => 'Номер телефона родителя обязателен для заполнения',
                'group.required' => 'Выберите группу',
                'datebirthday.required' => 'Дата рождения обязательна для заполнения',
            ]);

            // Находим студента
            $student = Student::where('users_id', $validated['users_id'])->first();
            if (!$student) {
                throw new \Exception('Студент не найден');
            }

            // Находим пользователя
            $user = User::find($validated['users_id']);
            if (!$user) {
                throw new \Exception('Пользователь не найден');
            }

            // Обновляем пользователя
            $user->update([
                'name' => $validated['login'],
                'email' => $validated['email'],
            ]);

            // Обновляем пароль, если он указан
            if ($request->filled('password')) {
                $user->update([
                    'password' => bcrypt($request->password)
                ]);
            }

            // Получаем курсы для новой группы
            $group = Group::where('name', $validated['group'])->first();
            if (!$group) {
                throw new \Exception('Группа "' . $validated['group'] . '" не найдена');
            }
            
            $courseNames = $group->getCourseNames();
            if (empty($courseNames)) {
                $courseNames = [];
            }

            // Преобразуем достижения
            $achievements = $validated['achievements'] ? array_filter(explode("\n", str_replace("\r", "", $validated['achievements']))) : [];

            // Обновляем студента
            $student->update([
                'fio' => $validated['fio'],
                'datebirthday' => $validated['datebirthday'],
                'numberphone' => $validated['numberphone'],
                'email' => $validated['email'],
                'numberparent' => $validated['numberparent'],
                'femaleparent' => $validated['femaleparent'],
                'group_name' => $validated['group'],
                'subjects' => $courseNames,
                'achievements' => $achievements,
            ]);

            DB::commit();
            \Log::info('Студент успешно отредактирован', ['student_id' => $student->id]);
            
            return redirect()->back()->with('success', 'Студент успешно отредактирован');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при редактировании студента', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Произошла ошибка при редактировании студента: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function add(Request $request) {
        try {
            DB::beginTransaction();
            
            \Log::info('Начало создания студента', ['request_data' => $request->all()]);
            
            // Валидация данных
            $validated = $request->validate([
                'name' => 'required|unique:users,name',
                'password' => 'required',
                'fio' => 'required',
                'numberphone' => 'required',
                'email' => 'required|email|unique:students,email',
                'femaleparent' => 'required',
                'numberparent' => 'required',
                'group' => 'required',
                'datebirthday' => 'required',
                'achievements' => '',
            ], [
                'name.required' => 'Логин обязателен для заполнения',
                'name.unique' => 'Пользователь с таким логином уже существует',
                'password.required' => 'Пароль обязателен для заполнения',
                'fio.required' => 'ФИО обязательно для заполнения',
                'numberphone.required' => 'Номер телефона обязателен для заполнения',
                'email.required' => 'Email обязателен для заполнения',
                'email.email' => 'Введите корректный email',
                'email.unique' => 'Студент с таким email уже существует',
                'femaleparent.required' => 'ФИО родителя обязательно для заполнения',
                'numberparent.required' => 'Номер телефона родителя обязателен для заполнения',
                'group.required' => 'Выберите группу',
                'datebirthday.required' => 'Дата рождения обязательна для заполнения',
            ]);

            \Log::info('Данные прошли валидацию', ['validated_data' => $validated]);

            // Создаем пользователя
            $user = User::create([
                'name' => $validated['name'],
                'password' => bcrypt($validated['password']),
                'email' => $validated['email'],
                'role' => 'student',
                
            ]);

            \Log::info('Пользователь создан', ['user_id' => $user->id]);

            if (!$user) {
                throw new \Exception('Не удалось создать пользователя');
            }

            // Преобразуем текстовые поля в массивы
            
            $achievements = $validated['achievements'] ? array_filter(explode("\n", str_replace("\r", "", $validated['achievements']))) : [];
            $group = Group::where('name', $validated['group'])->first();
            
            if (!$group) {
                throw new \Exception('Группа "' . $validated['group'] . '" не найдена');
            }
            
            // Получаем названия курсов для группы
            $courseNames = $group->getCourseNames();
            
            // Если курсов нет, устанавливаем пустой массив
            if (empty($courseNames)) {
                $courseNames = [];
                \Log::warning('У группы "' . $validated['group'] . '" нет связанных курсов');
            }

            \Log::info('Подготовлены данные для создания студента', [
                'achievements' => $achievements,
                'course_names' => $courseNames,
                'group_id' => $group->id
            ]);

            // Создаем преподавателя
            $student = Student::create([
                'users_id' => $user->id,
                'fio' => $validated['fio'],
                'datebirthday' => $validated['datebirthday'],
                'datewelcome' => date(today()),
                'numberphone' => $validated['numberphone'],
                'achievements' => $validated['achievements'],
                'email' => $validated['email'],
                'numberparent' => $validated['numberparent'],
                'femaleparent' => $validated['femaleparent'],
                'group_name' => $validated['group'],
                'subjects' => $courseNames,
                'average_performance' => 0,
                'average_attendance' => 0,
                'average_exam_score' => 0
            ]);

            \Log::info('студент создан', ['student_id' => $student->id]);

            if (!$student) {
                throw new \Exception('Не удалось создать студента');
            }

            DB::commit();
            \Log::info('Транзакция успешно завершена');
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Студент успешно добавлен']);
            }
            
            return redirect()->back()->with('success', 'студент успешно добавлен');
    } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при создании студента', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'success' => false,
                        'errors' => $e->errors()
                    ], 422);
                }
                
                return response()->json([
                    'success' => false, 
                    'message' => 'Произошла ошибка при добавлении студента: ' . $e->getMessage()
                ], 422);
            }
            
            return redirect()->back()
                ->with('error', 'Произошла ошибка при добавлении студента: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function delete(Request $request) {
        try {
            DB::beginTransaction();
            
            $userId = $request->input('users_id');
            
            if (!$userId) {
                throw new \Exception('ID пользователя не указан');
            }

            // Находим студента
            $student = Student::where('users_id', $userId)->first();
            if (!$student) {
                throw new \Exception('Студент не найден');
            }

            // Сохраняем имя для сообщения
            $studentName = $student->fio;

            // Удаляем студента
            $student->delete();

            // Удаляем пользователя
            $user = User::find($userId);
            if ($user) {
                $user->delete();
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'Студент "' . $studentName . '" успешно удален');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Ошибка при удалении студента', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Произошла ошибка при удалении студента: ' . $e->getMessage());
        }
    }

    // --- Аккаунт студента ---
    public function account() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        $student = $user->student;
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
        return view('student.account', compact('student', 'reviews'));
    }
    public function calendar() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        return view('student.calendar');
    }
    public function homework() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        $student = $user->student;
        $groupId = $student->group->id;
        $homeworks = \App\Models\HomeWork::with(['course', 'teacher', 'group', 'homeWorkStudents' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])
        ->where('groups_id', $groupId)
        ->orderBy('deadline', 'desc')
        ->get();
        return view('student.homework', compact('homeworks', 'student'));
    }
    public function grades() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        $student = $user->student ?? null;
        $grades = collect();
        $gradeStats = [
            'fives' => 0,
            'fours' => 0,
            'threes' => 0,
            'twos' => 0
        ];
        $average = 0;
        $average_exam = 0;
        $attendance = 0;
        
        if ($student) {
            $statistics = $student->statistics()->get();
            $allGrades = [];
            $gradeStats = [
                'fives' => 0,
                'fours' => 0,
                'threes' => 0,
                'twos' => 0
            ];
            foreach ($statistics as $stat) {
                // Оценки за уроки
                if ($stat->grade_lesson > 0) {
                    $allGrades[] = $stat->grade_lesson;
                    if ($stat->grade_lesson >= 4.5) $gradeStats['fives']++;
                    elseif ($stat->grade_lesson >= 3.5) $gradeStats['fours']++;
                    elseif ($stat->grade_lesson >= 2.5) $gradeStats['threes']++;
                    else $gradeStats['twos']++;
                }
                // Оценки за домашки
                if ($stat->homework > 0) {
                    $allGrades[] = $stat->homework;
                    if ($stat->homework >= 4.5) $gradeStats['fives']++;
                    elseif ($stat->homework >= 3.5) $gradeStats['fours']++;
                    elseif ($stat->homework >= 2.5) $gradeStats['threes']++;
                    else $gradeStats['twos']++;
                }
            }
            $average = count($allGrades) > 0 ? round(array_sum($allGrades) / count($allGrades), 2) : 0;
            $average_exam = $average; // Пока считаем экзамен как общий средний балл
            $totalLessons = $statistics->count();
            $attendedLessons = $statistics->where('attendance', true)->count();
            $attendance = $totalLessons > 0 ? round($attendedLessons / $totalLessons * 100, 1) : 0;
            $grades = $statistics->sortByDesc('created_at');
        }
        
        return view('student.grades', compact('user', 'grades', 'gradeStats', 'average', 'average_exam', 'attendance'));
    }
    public function attendance() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        $student = $user->student ?? null;
        $attendance = collect();
        if ($student) {
            $attendance = $student->statistics()
                ->whereNotNull('attendance')
                ->orderByDesc('created_at')
                ->get();
        }
        return view('student.attendance', compact('user', 'attendance'));
    }
    public function notifications() {
        $user = auth()->user();
        
        // Демо-данные для уведомлений (в реальной системе это будет из БД)
        $notifications = collect([
            [
                'id' => 1,
                'type' => 'homework',
                'title' => 'Новое домашнее задание',
                'message' => 'Добавлено новое домашнее задание по курсу "Математика"',
                'date' => now()->subHours(2),
                'read' => false,
                'icon' => '📚'
            ],
            [
                'id' => 2,
                'type' => 'grade',
                'title' => 'Получена оценка',
                'message' => 'Ваша работа по курсу "Физика" оценена на 5 баллов',
                'date' => now()->subDay(),
                'read' => false,
                'icon' => '⭐'
            ],
            [
                'id' => 3,
                'type' => 'schedule',
                'title' => 'Изменение расписания',
                'message' => 'Занятие по курсу "Химия" перенесено на 15:00',
                'date' => now()->subDays(2),
                'read' => true,
                'icon' => '📅'
            ],
            [
                'id' => 4,
                'type' => 'system',
                'title' => 'Системное уведомление',
                'message' => 'Система будет недоступна с 23:00 до 01:00 для технического обслуживания',
                'date' => now()->subDays(3),
                'read' => true,
                'icon' => '🔧'
            ],
            [
                'id' => 5,
                'type' => 'homework',
                'title' => 'Напоминание о дедлайне',
                'message' => 'Домашнее задание по курсу "История" нужно сдать до завтра',
                'date' => now()->subDays(4),
                'read' => true,
                'icon' => '⏰'
            ]
        ]);
        
        return view('student.notifications', compact('user', 'notifications'));
    }

    public function submitHomework(Request $request, $homeworkId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // до 10 МБ
        ]);
        $student = auth()->user()->student;
        $homework = \App\Models\HomeWork::findOrFail($homeworkId);

        // Сохраняем файл
        $path = $request->file('file')->store('homework_submissions', 'public');

        // Создаём или обновляем запись сдачи
        $submission = \App\Models\HomeWorkStudent::updateOrCreate(
            [
                'home_work_id' => $homework->id,
                'student_id' => $student->id,
            ],
            [
                'file_path' => $path,
                'grade' => null,
                'feedback' => null,
            ]
        );

        return redirect()->back()->with('success', 'Работа успешно отправлена!');
    }

    public function appeals() {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        $student = $user->student;
        $admins = \App\Models\User::where('role', 'admin')->get();
        $teachers = collect();
        if ($student && $student->group) {
            $courseIds = $student->group->courses ?? [];
            if (!is_array($courseIds)) {
                $courseIds = json_decode($courseIds, true) ?? [];
            }
            $teacherIds = collect();
            foreach ($courseIds as $courseId) {
                $course = \App\Models\Course::find($courseId);
                if ($course && $course->access_) {
                    $access = is_array($course->access_) ? $course->access_ : json_decode($course->access_, true);
                    if (isset($access['teachers']) && is_array($access['teachers'])) {
                        foreach ($access['teachers'] as $teacherId) {
                            // Фильтруем только числовые id
                            if (is_numeric($teacherId)) {
                                $teacherIds->push((int)$teacherId);
                            }
                        }
                    }
                }
            }
            $teacherIds = $teacherIds->unique()->filter();
            // Получаем преподавателей с их ФИО из таблицы teachers
            if ($teacherIds->isNotEmpty()) {
                $teachers = \App\Models\Teacher::whereIn('users_id', $teacherIds)->get();
            }
        }
        
        // Получаем обращения где студент отправитель или получатель
        $appeals = \App\Models\Appeal::where('sender_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
            
        // Разделяем обращения на отправленные и полученные
        $sentAppeals = $appeals->where('sender_id', $user->id);
        $receivedAppeals = $appeals->where('recipient_id', $user->id);
        
        return view('student.appeals', compact('user', 'appeals', 'sentAppeals', 'receivedAppeals', 'admins', 'teachers'));
    }

    public function sendAppeal(Request $request) {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
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
        return redirect()->route('student.appeals')->with('success', 'Обращение отправлено!');
    }

    public function replyToAppeal(Request $request, $id) {
        $user = auth()->user();
        if ($user->role !== 'student') {
            abort(403, 'Доступ запрещён');
        }
        
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

    public function adminView($id) {
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
        return view('admin.student', compact('student', 'reviews'));
    }
}
