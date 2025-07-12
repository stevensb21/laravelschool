<?php

namespace App\Models;
use App\CrudDb;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use CrudDb;
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'username',
        'password',
        'email',
        'name',
        'role'
    ];

    protected $hidden = [
        'password',
    ];

    public function teacher()
   {
       return $this->hasOne(Teacher::class, 'users_id');
   }

   public function student()
   {
       return $this->hasOne(Student::class, 'users_id');
   }
   
}
