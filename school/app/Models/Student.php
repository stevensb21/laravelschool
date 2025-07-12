<?php

namespace App\Models;
use App\CrudDb;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use CrudDb;
    protected $table = 'students';
    protected $quarded = [];

     protected $fillable = [
        'users_id',
        'fio',
        'datebirthday',
        'datewelcome',
        'numberphone',
        'email',
        'numberparent',
        'femaleparent',
        'group_name',
        'subjects',
        'average_performance',
        'average_attendance',
        'average_exam_score',
        'achievements'

    ];
    

    protected $casts = [
        'subjects' => 'array',
        'achievements' => 'array',
        'average_performance' => 'float',
        'average_attendance' => 'float',
        'average_exam_score' => 'float'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    
    public function statistics()
    {
        return $this->hasMany(Statistic::class, 'student_id');
    }
    
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_name', 'name');
    }
}
