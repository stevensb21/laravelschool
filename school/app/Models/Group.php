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

    public function students()
    {
        return $this->hasMany(Student::class, 'group_name', 'name');
    }
}
