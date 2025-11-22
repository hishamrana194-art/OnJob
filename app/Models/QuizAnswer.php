<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $fillable = [
        'question_id',
        'answer',
        'is_correct'
    ];
    
}
