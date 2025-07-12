<?php

namespace App\Models;

use App\CrudDb;
use Illuminate\Database\Eloquent\Model;



class Calendar extends Model
{
    use CrudDb;

    protected $table = 'calendars';
    protected $fillable = [
        'date_', 
        'subject', 
        'name_group', 
        'start_time', 
        'end_time', 
        'teacher'
    ];

    

}
