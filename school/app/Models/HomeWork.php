<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HomeWork extends Model
{
    protected $table = 'home_works';
    
    protected $fillable = [
        'groups_id',
        'course_id', 
        'teachers_id',
        'deadline',
        'description',
        'file_path',
        'status',
        'method_id'
    ];

    /**
     * Boot метод для автоматического обновления статуса
     */
    protected static function boot()
    {
        parent::boot();
        
        // Автоматически обновляем статус при сохранении
        static::saving(function ($homework) {
            // Проверяем, изменилась ли дата deadline
            if ($homework->isDirty('deadline')) {
                $homework->updateStatusIfNeeded();
            }
        });
    }

    /**
     * Обновляет статус домашнего задания если это необходимо
     */
    public function updateStatusIfNeeded()
    {
        $newStatus = $this->calculateStatus();
        $currentStatus = $this->getRawOriginal('status');
        
        if ($currentStatus !== $newStatus) {
            $this->attributes['status'] = $newStatus;
        }
    }

    /**
     * Рассчитывает правильный статус для домашнего задания
     */
    public function calculateStatus()
    {
        $now = now();
        $deadline = Carbon::parse($this->deadline);
        
        // Проверяем, все ли студенты получили оценки
        $totalStudents = $this->group->students->count();
        $gradedStudents = $this->homeWorkStudents->whereNotNull('grade')->count();
        $allGraded = $totalStudents > 0 && $gradedStudents == $totalStudents;
        
        if ($allGraded) {
            return 'Завершено';
        } elseif ($deadline < $now) {
            return 'Просрочено';
        } else {
            return 'Активно';
        }
    }

    /**
     * Аксессор для получения актуального статуса
     */
    public function getStatusAttribute($value)
    {
        // Проверяем, нужно ли обновить статус
        $calculatedStatus = $this->calculateStatus();
        
        if ($value !== $calculatedStatus) {
            // Используем getRawOriginal чтобы избежать рекурсии
            $this->attributes['status'] = $calculatedStatus;
            $this->saveQuietly(); // Сохраняем без вызова событий
            return $calculatedStatus;
        }
        
        return $value;
    }

    /**
     * Сохраняет модель без вызова событий
     */
    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            return $this->save($options);
        });
    }

    /**
     * Статический метод для обновления статуса всех домашних заданий
     */
    public static function updateAllStatuses()
    {
        $homeworks = self::with(['group.students', 'homeWorkStudents'])->get();
        $updated = 0;
        
        foreach ($homeworks as $homework) {
            $oldStatus = $homework->getRawOriginal('status');
            $newStatus = $homework->calculateStatus();
            
            if ($oldStatus !== $newStatus) {
                $homework->update(['status' => $newStatus]);
                $updated++;
            }
        }
        
        return $updated;
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function method()
    {
        return $this->belongsTo(Method::class, 'method_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teachers_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'groups_id');
    }

    public function homeWorkStudents()
    {
        return $this->hasMany(HomeWorkStudent::class, 'home_work_id');
    }
}
