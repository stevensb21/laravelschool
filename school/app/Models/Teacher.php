<?php

namespace App\Models;
use App\CrudDb;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Teacher extends Model
{
    use CrudDb;
    protected $table = 'teachers';
    protected $guarded = [];

    protected $fillable = [
        'users_id',
        'fio',
        'job_title',
        'email',
        'subjects',
        'education',
        'achievements',
        'average_performance',
        'average_attendance',
        'average_exam_score'
    ];

    protected $casts = [
        'subjects' => 'array',
        'education' => 'array',
        'achievements' => 'array',
        'average_performance' => 'float',
        'average_attendance' => 'float',
        'average_exam_score' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class, 'recipient_id')
            ->where('recipient_type', 'teacher');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 1);
    }

    /**
     * Вычисляет и обновляет статистику преподавателя на основе оценок студентов из его групп
     */
    public function calculateAndUpdateStatistics()
    {
        // Получаем курсы, которые ведет преподаватель
        $courseIds = \App\Models\Course::where(function($q) {
            $q->whereJsonContains('access_->teachers', (int)$this->users_id)
              ->orWhereJsonContains('access_->teachers', (string)$this->users_id);
        })->pluck('id')->toArray();

        // Получаем группы, где преподает этот преподаватель
        $groupNames = [];
        if (!empty($courseIds)) {
            $groupNames = \App\Models\Group::where(function($q) use ($courseIds) {
                foreach ($courseIds as $cid) {
                    $q->orWhereJsonContains('courses', (int)$cid)
                      ->orWhereJsonContains('courses', (string)$cid);
                }
            })->pluck('name')->toArray();
        }

        // Получаем студентов из этих групп
        $studentIds = \App\Models\Student::whereIn('group_name', $groupNames)->pluck('id')->toArray();

        // Вычисляем средние показатели из базы данных
        $averagePerformance = 0;
        $averageAttendance = 0;
        $averageExamScore = 0;

        if (!empty($studentIds)) {
            // Средняя успеваемость (из таблицы statistics - оценки за уроки)
            $performanceStats = \App\Models\Statistic::whereIn('student_id', $studentIds)
                ->where('grade_lesson', '>', 0)
                ->get();
            
            if ($performanceStats->count() > 0) {
                $averagePerformance = round($performanceStats->avg('grade_lesson'), 1);
            }

            // Средняя посещаемость (процент присутствующих на уроках)
            $totalLessons = \App\Models\Statistic::whereIn('student_id', $studentIds)->count();
            $attendedLessons = \App\Models\Statistic::whereIn('student_id', $studentIds)
                ->where('grade_lesson', '>', 0)
                ->count();
            
            if ($totalLessons > 0) {
                $averageAttendance = round(($attendedLessons / $totalLessons) * 100, 1);
            }

            // Средняя за экзамены (из домашних заданий, где grade > 0)
            $examStats = \App\Models\HomeWorkStudent::whereIn('student_id', $studentIds)
                ->where('grade', '>', 0)
                ->get();
            
            if ($examStats->count() > 0) {
                $averageExamScore = round($examStats->avg('grade'), 1);
            }
        }

        // Обновляем данные преподавателя с вычисленными значениями
        $this->update([
            'average_performance' => $averagePerformance,
            'average_attendance' => $averageAttendance,
            'average_exam_score' => $averageExamScore
        ]);

        return [
            'average_performance' => $averagePerformance,
            'average_attendance' => $averageAttendance,
            'average_exam_score' => $averageExamScore
        ];
    }

    /**
     * Получает статистику преподавателя без обновления базы данных
     */
    public function getStatistics()
    {
        // Получаем курсы, которые ведет преподаватель
        $courseIds = \App\Models\Course::where(function($q) {
            $q->whereJsonContains('access_->teachers', (int)$this->users_id)
              ->orWhereJsonContains('access_->teachers', (string)$this->users_id);
        })->pluck('id')->toArray();

        // Получаем группы, где преподает этот преподаватель
        $groupNames = [];
        if (!empty($courseIds)) {
            $groupNames = \App\Models\Group::where(function($q) use ($courseIds) {
                foreach ($courseIds as $cid) {
                    $q->orWhereJsonContains('courses', (int)$cid)
                      ->orWhereJsonContains('courses', (string)$cid);
                }
            })->pluck('name')->toArray();
        }

        // Получаем студентов из этих групп
        $studentIds = \App\Models\Student::whereIn('group_name', $groupNames)->pluck('id')->toArray();

        $averageLesson = 0; // средняя оценка за урок
        $averageAttendance = 0;
        $averageHomework = 0; // средняя оценка за домашнюю работу

        if (!empty($studentIds)) {
            // --- Только уроки, которые вел этот преподаватель ---
            $lessonStats = collect();
            $lessonIds = [];
            foreach ($studentIds as $sid) {
                $stats = \App\Models\Statistic::where('student_id', $sid)
                    ->get(); // убираем фильтр по grade_lesson
                foreach ($stats as $stat) {
                    if (preg_match('/lesson:(\\d+)/', $stat->notes, $m)) {
                        $lessonId = $m[1];
                        $calendar = \App\Models\Calendar::find($lessonId);
                        if ($calendar &&
                            str_replace(' ', '', mb_strtolower($calendar->teacher)) == str_replace(' ', '', mb_strtolower($this->fio))) {
                            $lessonStats->push($stat);
                            $lessonIds[] = $lessonId;
                        }
                    }
                }
            }
            if ($lessonStats->count() > 0) {
                $averageLesson = round($lessonStats->where('grade_lesson', '>', 0)->avg('grade_lesson'), 1);
                $totalLessons = $lessonStats->count();
                $attendedLessons = $lessonStats->where('attendance', true)->count();
                $averageAttendance = $totalLessons > 0 ? round($attendedLessons / $totalLessons * 100, 1) : 0;
                // Средняя за домашку — только по homework > 0 для этих же записей
                $averageHomework = round($lessonStats->where('homework', '>', 0)->avg('homework'), 1);
            }
            // --- Только домашки, которые задал этот преподаватель ---
            $homeworkStats = \App\Models\HomeWorkStudent::whereIn('student_id', $studentIds)
                ->where('grade', '>', 0)
                ->get()
                ->filter(function($hws) {
                    return $hws->homework && $hws->homework->teacher && $hws->homework->teacher->users_id == $this->users_id;
                });
            if ($homeworkStats->count() > 0) {
                $averageHomework = round($homeworkStats->avg('grade'), 1);
            }
        }
        $homeworkIds = $homeworkStats->map(function($hws) { return $hws->home_work_id; })->unique()->values()->all();
        \Log::info('TEACHER_STATS', [
            'fio' => $this->fio,
            'lessonIds' => $lessonIds,
            'homeworkIds' => $homeworkIds,
            'lessonStats_count' => $lessonStats->count(),
            'homeworkStats_count' => $homeworkStats->count(),
        ]);

        return [
            'average_performance' => $averageLesson, // средняя оценка за урок
            'average_attendance' => $averageAttendance,
            'average_homework' => $averageHomework // средняя за домашнее задание
        ];
    }
}
