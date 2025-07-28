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
    
    // Старое отношение для обратной совместимости
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_name', 'name');
    }
    
    // Новые отношения для множественных групп
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'student_group', 'student_id', 'group_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    
    public function primaryGroup()
    {
        return $this->belongsToMany(Group::class, 'student_group', 'student_id', 'group_id')
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }
    
    // Методы для работы с группами
    public function addToGroup($groupId, $isPrimary = false)
    {
        // Если добавляем как основную, снимаем флаг с других групп
        if ($isPrimary) {
            $this->groups()->updateExistingPivot($this->groups->pluck('id'), ['is_primary' => false]);
        }
        
        return $this->groups()->attach($groupId, ['is_primary' => $isPrimary]);
    }
    
    public function removeFromGroup($groupId)
    {
        return $this->groups()->detach($groupId);
    }
    
    public function setPrimaryGroup($groupId)
    {
        // Снимаем флаг с всех групп
        $this->groups()->updateExistingPivot($this->groups->pluck('id'), ['is_primary' => false]);
        
        // Устанавливаем новую основную группу
        return $this->groups()->updateExistingPivot($groupId, ['is_primary' => true]);
    }
    
    // Получить все предметы из всех групп студента
    public function getAllSubjects()
    {
        $subjects = collect();
        
        foreach ($this->groups as $group) {
            $groupSubjects = $group->courses()->pluck('name')->toArray();
            $subjects = $subjects->merge($groupSubjects);
        }
        
        return $subjects->unique()->toArray();
    }
    
    // Получить основную группу (для обратной совместимости)
    public function getPrimaryGroup()
    {
        return $this->primaryGroup()->first();
    }
    
    // Получить название основной группы (для обратной совместимости)
    public function getPrimaryGroupName()
    {
        $primaryGroup = $this->getPrimaryGroup();
        return $primaryGroup ? $primaryGroup->name : null;
    }
}
