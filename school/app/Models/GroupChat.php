<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    protected $table = 'group_chats';
    protected $fillable = ['group_id', 'name'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function userChats()
    {
        return $this->hasMany(UserChat::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
} 