<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
