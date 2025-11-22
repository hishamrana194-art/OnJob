<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{

      protected $fillable = [
        'learning_path_id',
        'assigned_to_user',
        'assigned_by',
        'due_date',
        
    ];

}
