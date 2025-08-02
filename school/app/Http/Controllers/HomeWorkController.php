<?php

namespace App\Http\Controllers;

use App\Models\HomeWork;
use App\Models\Course;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\HomeWorkStudent;
use App\Models\Statistic;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class HomeWorkController extends Controller
{
    public function index(Request $request)
    {
        $query = HomeWork::with(['course', 'teacher', 'group.students', 'homeWorkStudents', 'method']);

        // Фильтр по предмету
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Фильтр по группе
        if ($request->filled('group')) {
            $query->where('groups_id', $request->group);
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Фильтр по поиску
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('course', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher', function($q) use ($search) {
                      $q->where('fio', 'like', "%{$search}%");
                  });
            });
        }

        $homeworks = $query->get();
        
        // Принудительно обновляем статус всех домашних заданий
        foreach ($homeworks as $homework) {
            $homework->updateStatusIfNeeded();
            $homework->saveQuietly();
        }
        
        $courses = Course::all();
        $groups = Group::all();
        $teachers = Teacher::all();
        
        return view('admin/homework', compact('homeworks', 'courses', 'groups', 'teachers'));
    }   

    public function submissions($id)
    {
        $homework = HomeWork::with(['course', 'teacher', 'group.students', 'homeWorkStudents.student'])->findOrFail($id);
        
        // Получаем всех студентов группы
        $allStudents = $homework->group->students;
        
        // Получаем студентов, которые сдали работу
        $submittedStudents = $homework->homeWorkStudents;
        
        // Создаем массив для отображения
        $studentsList = [];
        
        foreach ($allStudents as $student) {
            $submission = $submittedStudents->where('student_id', $student->id)->first();
            
            $studentsList[] = [
                'student' => $student,
                'submission' => $submission,
                'hasSubmitted' => $submission !== null,
                'filePath' => $submission && $submission->file_path ? ('/storage/' . ltrim($submission->file_path, '/')) : null,
                'grade' => $submission ? $submission->grade : null,
                'feedback' => $submission ? $submission->feedback : null,
            ];
        }
        
        if (auth()->user() && auth()->user()->role === 'teacher') {
            return view('teacher/homework-submissions', compact('homework', 'studentsList'));
        } else {
            return view('admin/homework-submissions', compact('homework', 'studentsList'));
        }
    }

    public function grade(Request $request)
    {
        try {
            $request->validate([
                'submission_id' => 'required|exists:home_work_students,id',
                'grade' => 'required|integer|between:2,5',
                'feedback' => 'nullable|string|max:500'
            ]);

            $submission = HomeWorkStudent::with('student')->findOrFail($request->submission_id);
            
            // Сохраняем оценку
            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback
            ]);

            // Добавляем запись в таблицу statistics
            Statistic::create([
                'student_id' => $submission->student_id,
                'grade_lesson' => 0, // Оценка за урок (не ставится при проверке ДЗ)
                'homework' => $request->grade, // Оценка за домашнее задание
                'attendance' => true, // Считаем, что студент присутствовал, раз сдал работу
                'notes' => 'homework:' . $submission->home_work_id . ';feedback:' . ($request->feedback ?: 'Без комментариев')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Оценка успешно сохранена и добавлена в статистику'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при сохранении оценки: ' . $e->getMessage()
            ], 500);
        }
    }

    public function extendDeadline(Request $request, $id)
    {
        try {
            $request->validate([
                'new_deadline' => 'required|date|after:today'
            ]);

            $homework = HomeWork::findOrFail($id);
            $homework->update([
                'deadline' => $request->new_deadline
            ]);

            // Обновляем статус после изменения даты
            $homework->updateStatusIfNeeded();
            $homework->saveQuietly();

            return response()->json([
                'success' => true,
                'message' => 'Срок сдачи успешно продлен до ' . $request->new_deadline,
                'new_deadline' => $request->new_deadline
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Ошибка при продлении срока домашнего задания', [
                'homework_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при продлении срока: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $homework = HomeWork::findOrFail($id);
            
            // Удаляем все связанные записи из home_work_students
            $homework->homeWorkStudents()->delete();
            
            // Удаляем само домашнее задание
            $homework->delete();

            return response()->json([
                'success' => true,
                'message' => 'Домашнее задание успешно удалено'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении задания: ' . $e->getMessage()
            ], 500);
        }
    }
}
