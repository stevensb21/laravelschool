<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'student_id',
        'grade_lesson',
        'homework',
        'attendance',
        'notes'
    ];

    protected $casts = [
        'grade_lesson' => 'decimal:2',
        'homework' => 'decimal:2',
        'attendance' => 'boolean'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
