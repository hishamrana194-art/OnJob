<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Question extends Model
{
    use HasTranslations;

    protected $fillable = [
        'quiz_id',
        'type',
        'question_text',
        'explanation',
        'image',
        'points',
        'order',
        'is_active',
    ];

    public array $translatable = ['question_text', 'explanation'];

    protected $casts = [
        'is_active' => 'boolean',
        'points' => 'integer',
        'order' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // For multiple_choice and single_choice
    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    // For matching type
    public function matches()
    {
        return $this->hasMany(QuestionMatch::class)->orderBy('order');
    }

    // For complete/fill-in-blank type
    public function blanks()
    {
        return $this->hasMany(QuestionBlank::class)->orderBy('order');
    }

    // Check question type
    public function isMultipleChoice()
    {
        return $this->type === 'multiple_choice';
    }

    public function isSingleChoice()
    {
        return $this->type === 'single_choice';
    }

    public function isComplete()
    {
        return $this->type === 'complete';
    }

    public function isMatching()
    {
        return $this->type === 'matching';
    }
}

