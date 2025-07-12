<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Method extends Model
{
    protected $table = 'methods';
    protected $guarded = [];

    protected $fillable = [
        'course_id',
        'title',
        'title_homework',
        'homework',
        'title_lesson',
        'lesson',
        'title_exercise',
        'exercise',
        'title_book',
        'book',
        'title_video',
        'video',
        'title_presentation',
        'presentation',
        'title_test',
        'test',
        'title_article',
        'article'
        
    ];

    protected $casts = [
        'homework' => 'array',
        'lesson' => 'array',
        'exercise' => 'array',
        'book' => 'array',
        'presentation' => 'array',
        'test' => 'array',
        'article' => 'array',
        'video' => 'array',
        
        'title_homework' => 'array',
        'title_lesson' => 'array',
        'title_exercise' => 'array',
        'title_book' => 'array',
        'title_video' => 'array',
        'title_presentation' => 'array',
        'title_test' => 'array',
        'title_article' => 'array'
        
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
