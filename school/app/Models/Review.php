<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
        'review_text',
        'rating',
        'status',
        'moderated_by',
        'moderation_comment',
        'moderated_at',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
    ];

    // Отправитель (через User)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Получатель (через User)
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    // Модератор (админ)
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // Получить имя отправителя
    public function getSenderNameAttribute()
    {
        if ($this->sender_type === 'teacher') {
            $teacher = Teacher::where('users_id', $this->sender_id)->first();
            return $teacher ? $teacher->fio : $this->sender->name ?? 'Пользователь';
        } else {
            $student = Student::where('users_id', $this->sender_id)->first();
            return $student ? $student->fio : $this->sender->name ?? 'Пользователь';
        }
    }

    // Получить имя получателя
    public function getRecipientNameAttribute()
    {
        if ($this->recipient_type === 'teacher') {
            $teacher = Teacher::find($this->recipient_id);
            return $teacher ? $teacher->fio : $this->recipient->name ?? 'Пользователь';
        } else {
            $student = Student::find($this->recipient_id);
            return $student ? $student->fio : $this->recipient->name ?? 'Пользователь';
        }
    }
}
