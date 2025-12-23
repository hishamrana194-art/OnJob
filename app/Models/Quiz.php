<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Quiz extends Model
{
    use HasTranslations;

    protected $fillable = [
        'module_item_id',
        'course_id',
        'title',
        'description',
        'time_limit',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_correct_answers',
        'is_active',
    ];

    public array $translatable = ['title', 'description'];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_active' => 'boolean',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
    ];

    public function moduleItem()
    {
        return $this->belongsTo(ModuleItem::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // Get total points for this quiz
    public function getTotalPointsAttribute()
    {
        return $this->questions->sum('points');
    }

    // Get questions count
    public function getQuestionsCountAttribute()
    {
        return $this->questions->count();
    }
}

