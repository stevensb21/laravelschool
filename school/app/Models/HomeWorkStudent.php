<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeWorkStudent extends Model
{
    protected $table = 'home_work_students';
    
    protected $fillable = [
        'home_work_id',
        'student_id', 
        'file_path',
        'grade',
        'feedback',
        
    ];

    public function homework()
    {
        return $this->belongsTo(HomeWork::class, 'home_work_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

}
