<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QuestionMatch extends Model
{
    use HasTranslations;

    protected $fillable = [
        'question_id',
        'left_side',
        'right_side',
        'order',
    ];

    public array $translatable = ['left_side', 'right_side'];

    protected $casts = [
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

