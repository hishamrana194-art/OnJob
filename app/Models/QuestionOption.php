<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QuestionOption extends Model
{
    use HasTranslations;

    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    public array $translatable = ['option_text'];

    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

