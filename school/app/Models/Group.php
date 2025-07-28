<?php

namespace App\Models;
use App\CrudDb;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use CrudDb;
    protected $table = 'groups';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'size',
        'average_rating',
        'average_attendance',
        'average_exam',
        'courses',
        'teacher_id',
    ];

    protected $casts = [
        'courses' => 'array',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_group', 'group_id', 'course_id');
    }

    /**
     * Получить названия всех курсов группы
     */
    public function getCourseNames()
    {
        return $this->courses()->pluck('name')->toArray();
    }

    // Старое отношение для обратной совместимости
    public function students()
    {
        return $this->hasMany(Student::class, 'group_name', 'name');
    }
    
    // Новые отношения для множественных студентов
    public function allStudents()
    {
        return $this->belongsToMany(Student::class, 'student_group', 'group_id', 'student_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    
    public function primaryStudents()
    {
        return $this->belongsToMany(Student::class, 'student_group', 'group_id', 'student_id')
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }
    
    // Методы для работы со студентами
    public function addStudent($studentId, $isPrimary = false)
    {
        return $this->allStudents()->attach($studentId, ['is_primary' => $isPrimary]);
    }
    
    public function removeStudent($studentId)
    {
        return $this->allStudents()->detach($studentId);
    }
    
    // Получить количество студентов в группе
    public function getStudentsCount()
    {
        return $this->allStudents()->count();
    }
    
    // Получить количество студентов, для которых это основная группа
    public function getPrimaryStudentsCount()
    {
        return $this->primaryStudents()->count();
    }
    
    // Отношение к чату группы
    public function groupChat()
    {
        return $this->hasOne(GroupChat::class);
    }
}
