<?php

namespace App\Models;

use App\CrudDb;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use CrudDb;
    protected $table = 'courses';
    protected $fillable = [
        'name',
        'access_'
    ];

    protected $casts = [
        'access_' => 'array'
    ];

    public static function getAll()
    {
        return self::pluck('name')->toArray();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'course_group', 'course_id', 'group_id');
    }
}

