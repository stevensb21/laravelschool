<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    protected $table = 'appeals';
    
    protected $fillable = [
        'title',
        'sender_id',
        'recipient_id',
        'type',
        'description',
        'feedback',
        'like_feedback',
        'status'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
