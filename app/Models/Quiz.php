<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
        protected $fillable = [
        'module_id',
        'passing_score'
    ];

    
}
