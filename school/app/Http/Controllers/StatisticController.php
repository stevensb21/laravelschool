<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Statistic;
use App\Models\Student;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\HomeWork;
use App\Models\Appeal;
use Illuminate\Support\Facades\DB;
use App\Exports\StatisticsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\GroupsStatsExport;

class StatisticController extends Controller
{
    public function index(Request $request) {
        // Получаем период из запроса
        $period = $request->get('period', 'all');
        
        // Определяем дату начала периода
        $startDate = null;
        switch ($period) {
            case 'week':
                $startDate = now()->subWeek();
                break;
            case 'month':
                $startDate = now()->subMonth();
                break;
            case 'year':
                $startDate = now()->subYear();
                break;
            default:
                $startDate = null; // Все время
        }
        
        // Все записи статистики с фильтром по дате
        $query = Statistic::query();
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        $all = $query->get();
        $total = $all->count();

        // Средний балл (только где был на уроке)
        $avg_grade = $all->where('grade_lesson', '>', 0)->avg('grade_lesson');
        $avg_grade = $avg_grade ? round($avg_grade, 2) : 0;

        // Посещаемость (процент)
        $attended = $all->where('grade_lesson', '>', 0)->count();
        $attendance = $total > 0 ? round($attended / $total * 100, 1) : 0;

        // Средний балл за домашку (только где > 0)
        $avg_homework = $all->where('homework', '>', 0)->avg('homework');
        $avg_homework = $avg_homework ? round($avg_homework, 2) : 0;

        // Распределение по оценкам (отличники, хорошисты и т.д.)
        $grades = $all->where('grade_lesson', '>', 0)->pluck('grade_lesson');
        $excellent = $grades->filter(function($grade) { return $grade >= 4.5; })->count();
        $good = $grades->filter(function($grade) { return $grade >= 3.5 && $grade < 4.5; })->count();
        $satisfactory = $grades->filter(function($grade) { return $grade >= 2.5 && $grade < 3.5; })->count();
        $unsatisfactory = $grades->filter(function($grade) { return $grade < 2.5; })->count();
        
        $total_grades = $grades->count();
        $excellent_percent = $total_grades > 0 ? round($excellent / $total_grades * 100) : 0;
        $good_percent = $total_grades > 0 ? round($good / $total_grades * 100) : 0;
        $satisfactory_percent = $total_grades > 0 ? round($satisfactory / $total_grades * 100) : 0;
        $unsatisfactory_percent = $total_grades > 0 ? round($unsatisfactory / $total_grades * 100) : 0;

        // Категории посещаемости по студентам
        $all_students = Student::with('statistics')->get();
        $excellent_attendance = $all_students->filter(function($student) {
            $lessonStats = $student->statistics->filter(function($stat) {
                return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
            });
            $total = $lessonStats->count();
            $attended = $lessonStats->where('attendance', true)->count();
            $percent = $total > 0 ? ($attended / $total) * 100 : 0;
            return $percent >= 90;
        })->count();
        $good_attendance = $all_students->filter(function($student) {
            $lessonStats = $student->statistics->filter(function($stat) {
                return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
            });
            $total = $lessonStats->count();
            $attended = $lessonStats->where('attendance', true)->count();
            $percent = $total > 0 ? ($attended / $total) * 100 : 0;
            return $percent >= 80 && $percent < 90;
        })->count();
        $satisfactory_attendance = $all_students->filter(function($student) {
            $lessonStats = $student->statistics->filter(function($stat) {
                return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
            });
            $total = $lessonStats->count();
            $attended = $lessonStats->where('attendance', true)->count();
            $percent = $total > 0 ? ($attended / $total) * 100 : 0;
            return $percent >= 70 && $percent < 80;
        })->count();
        $unsatisfactory_attendance = $all_students->filter(function($student) {
            $lessonStats = $student->statistics->filter(function($stat) {
                return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
            });
            $total = $lessonStats->count();
            $attended = $lessonStats->where('attendance', true)->count();
            $percent = $total > 0 ? ($attended / $total) * 100 : 0;
            return $percent < 70;
        })->count();

        // Категории успеваемости по средним баллам студентов
        $students = \App\Models\Student::with('statistics')->get();
        $student_averages = [];
        foreach ($students as $student) {
            $grades = $student->statistics->where('grade_lesson', '>', 0)->pluck('grade_lesson');
            if ($grades->count() > 0) {
                $student_averages[] = $grades->avg();
            }
        }
        $total_students = count($student_averages);
        $excellent = collect($student_averages)->filter(fn($avg) => $avg >= 4.5)->count();
        $good = collect($student_averages)->filter(fn($avg) => $avg >= 3.5 && $avg < 4.5)->count();
        $satisfactory = collect($student_averages)->filter(fn($avg) => $avg >= 2.5 && $avg < 3.5)->count();
        $unsatisfactory = collect($student_averages)->filter(fn($avg) => $avg < 2.5)->count();
        $excellent_percent = $total_students > 0 ? round($excellent / $total_students * 100) : 0;
        $good_percent = $total_students > 0 ? round($good / $total_students * 100) : 0;
        $satisfactory_percent = $total_students > 0 ? round($satisfactory / $total_students * 100) : 0;
        $unsatisfactory_percent = $total_students > 0 ? round($unsatisfactory / $total_students * 100) : 0;
        // Средний балл по системе теперь среднее от средних студентов
        $avg_grade = $total_students > 0 ? round(array_sum($student_averages) / $total_students, 2) : 0;

        // Статистика преподавателей
        $teachers_count = Teacher::count();
        $active_teachers = Teacher::count(); // Убираем проверку статуса, так как столбца нет
        $avg_teacher_rating = 4.7; // Можно добавить поле рейтинга в таблицу teachers
        $avg_assignments = HomeWork::count() / max($teachers_count, 1);
        $avg_response_time = 2; // Можно добавить поле времени ответа

        // Статистика домашних заданий (на основе реальных сдач студентов)
        $all_groups = Group::with('students')->get();
        $total_students_in_groups = $all_groups->sum(function($group) {
            return $group->students->count();
        });
        $total_homeworks = \App\Models\HomeWork::count();
        $expected_submissions = $total_homeworks * max($total_students_in_groups, 1);
        
        // Фильтруем сдачи домашних заданий по периоду
        $homework_submissions_query = \App\Models\HomeWorkStudent::query();
        if ($startDate) {
            $homework_submissions_query->where('created_at', '>=', $startDate);
        }
        $homework_submissions = $homework_submissions_query->get();
        
        // Сдано вовремя (есть оценка)
        $completed_submissions = $homework_submissions->whereNotNull('grade')->count();
        
        // Сдано с опозданием (есть файл, но нет оценки)
        $overdue_submissions = $homework_submissions->whereNotNull('file_path')->whereNull('grade')->count();
        
        // Не сдано (ожидаемых сдач минус те, кто сдал)
        $total_submitted = $completed_submissions + $overdue_submissions;
        $not_submitted = max(0, $expected_submissions - $total_submitted);
        
        if ($expected_submissions == 0) {
            $completed_percent = 0;
            $overdue_percent = 0;
            $pending_percent = 0;
        } else {
            $completed_percent = round($completed_submissions / $expected_submissions * 100, 1);
            $overdue_percent = round($overdue_submissions / $expected_submissions * 100, 1);
            $pending_percent = round($not_submitted / $expected_submissions * 100, 1);
        }

        // Статистика по группам
        $groups_stats = Group::with(['students.statistics'])->get()->map(function($group) {
            $students = $group->students;
            $total_students = $students->count();
            if ($total_students == 0) {
                return [
                    'name' => $group->name,
                    'avg_grade' => 0,
                    'attendance' => 0,
                    'homework_completion' => 0,
                    'activity' => 'Низкая'
                ];
            }
            $all_grades = collect();
            $all_attendance = collect();
            $all_homework = collect();
            foreach ($students as $student) {
                $statistics = $student->statistics;
                $lessonStats = $statistics->filter(function($stat) {
                    return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
                });
                $all_attendance = $all_attendance->merge($lessonStats);
                $all_grades = $all_grades->merge($lessonStats->where('grade_lesson', '>', 0)->pluck('grade_lesson'));
                $all_homework = $all_homework->merge($statistics->where('homework', '>', 0)->pluck('homework'));
            }
            $totalLessons = $all_attendance->count();
            $attendedLessons = $all_attendance->where('attendance', true)->count();
            $attendance = $totalLessons > 0 ? round($attendedLessons / $totalLessons * 100, 1) : 0;
            $avg_grade = $all_grades->count() > 0 ? round($all_grades->avg(), 1) : 0;
            $homework_completion = $all_homework->count() > 0 ? round($all_homework->avg(), 1) : 0;
            // Определяем активность группы
            $activity = 'Низкая';
            if ($avg_grade >= 4.5 && $attendance >= 90) {
                $activity = 'Высокая';
            } elseif ($avg_grade >= 4.0 && $attendance >= 80) {
                $activity = 'Средняя';
            }
            return [
                'name' => $group->name,
                'avg_grade' => $avg_grade,
                'attendance' => $attendance,
                'homework_completion' => $homework_completion,
                'activity' => $activity
            ];
        });

        return view('admin.statistic', [
            'period' => $period,
            'avg_grade' => $avg_grade,
            'attendance' => $attendance,
            'avg_homework' => $avg_homework,
            'excellent_percent' => $excellent_percent,
            'good_percent' => $good_percent,
            'satisfactory_percent' => $satisfactory_percent,
            'unsatisfactory_percent' => $unsatisfactory_percent,
            'excellent_attendance' => $excellent_attendance,
            'good_attendance' => $good_attendance,
            'satisfactory_attendance' => $satisfactory_attendance,
            'unsatisfactory_attendance' => $unsatisfactory_attendance,
            'teachers_count' => $active_teachers,
            'avg_teacher_rating' => $avg_teacher_rating,
            'avg_assignments' => round($avg_assignments),
            'avg_response_time' => $avg_response_time,
            'completed_percent' => $completed_percent,
            'overdue_percent' => $overdue_percent,
            'pending_percent' => $pending_percent,
            'groups_stats' => $groups_stats,
        ]);
    }

    public function addTestData()
    {
        $studentId = \App\Models\Student::inRandomOrder()->value('id');
        if (!$studentId) {
            return response()->json(['error' => 'Нет студентов в базе'], 400);
        }
        for ($i = 0; $i < 10; $i++) {
            \App\Models\Statistic::create([
                'student_id' => $studentId,
                'grade_lesson' => rand(0, 5) > 0 ? round(rand(20, 50) / 10, 1) : 0,
                'homework' => rand(0, 5) > 0 ? round(rand(20, 50) / 10, 1) : 0,
                'attendance' => rand(0, 5) > 0,
                'notes' => rand(0, 1) ? 'Тестовая запись' : null,
            ]);
        }
        return response()->json(['success' => '10 тестовых записей добавлено!']);
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'all');
        $startDate = null;
        switch ($period) {
            case 'week': $startDate = now()->subWeek(); break;
            case 'month': $startDate = now()->subMonth(); break;
            case 'year': $startDate = now()->subYear(); break;
        }

        $groups_stats = \App\Models\Group::with(['students.statistics'])->get()->map(function($group) {
            $students = $group->students;
            $total_students = $students->count();
            if ($total_students == 0) {
                return [
                    'name' => $group->name,
                    'avg_grade' => 0,
                    'attendance' => 0,
                    'homework_completion' => 0,
                    'activity' => 'Низкая'
                ];
            }
            $all_grades = collect();
            $all_attendance = collect();
            $all_homework = collect();
            foreach ($students as $student) {
                $statistics = $student->statistics;
                $lessonStats = $statistics->filter(function($stat) {
                    return strpos(trim(strtolower($stat->notes)), 'lesson:') === 0;
                });
                $all_attendance = $all_attendance->merge($lessonStats);
                $all_grades = $all_grades->merge($lessonStats->where('grade_lesson', '>', 0)->pluck('grade_lesson'));
                $all_homework = $all_homework->merge($statistics->where('homework', '>', 0)->pluck('homework'));
            }
            $totalLessons = $all_attendance->count();
            $attendedLessons = $all_attendance->where('attendance', true)->count();
            $attendance = $totalLessons > 0 ? round($attendedLessons / $totalLessons * 100, 1) : 0;
            $avg_grade = $all_grades->count() > 0 ? round($all_grades->avg(), 1) : 0;
            $homework_completion = $all_homework->count() > 0 ? round($all_homework->avg(), 1) : 0;
            $activity = 'Низкая';
            if ($avg_grade >= 4.5 && $attendance >= 90) $activity = 'Высокая';
            elseif ($avg_grade >= 4.0 && $attendance >= 80) $activity = 'Средняя';
            return [
                'name' => $group->name ?? '',
                'avg_grade' => isset($avg_grade) && $avg_grade !== null ? (float)$avg_grade : 0,
                'attendance' => isset($attendance) && $attendance !== null ? (float)$attendance : 0,
                'homework_completion' => isset($homework_completion) && $homework_completion !== null ? (float)$homework_completion : 0,
                'activity' => $activity ?? 'Низкая'
            ];
        });

        $format = $request->get('format', 'csv');
        $filename = 'statistics_' . now()->format('Ymd_His') . '.' . $format;

        if ($format === 'pdf') {
            $pdf = \PDF::loadView('admin.statistic_pdf', [
                'groups_stats' => $groups_stats
            ]);
            return $pdf->download($filename);
        } elseif ($format === 'xlsx') {
            return Excel::download(new GroupsStatsExport($groups_stats), $filename);
        } elseif ($format === 'csv') {
            return $this->exportToCsv($groups_stats, $filename);
        } else {
            return $this->exportToCsv($groups_stats, $filename); // Пока только CSV и PDF
        }
    }

    private function exportToCsv($groups_stats, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($groups_stats) {
            $file = fopen('php://output', 'w');
            
            // Заголовки
            fputcsv($file, ['Группа', 'Средний балл', 'Посещаемость (%)', 'Выполнение ДЗ', 'Активность']);
            
            // Данные
            foreach ($groups_stats as $group) {
                fputcsv($file, [
                    $group['name'],
                    $group['avg_grade'],
                    $group['attendance'],
                    $group['homework_completion'],
                    $group['activity'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
