<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QuestionBlank extends Model
{
    use HasTranslations;

    protected $fillable = [
        'question_id',
        'blank_position',
        'correct_answer',
        'acceptable_answers',
        'order',
    ];

    public array $translatable = ['correct_answer', 'acceptable_answers'];

    protected $casts = [
        'blank_position' => 'integer',
        'order' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Check if an answer is correct (including acceptable answers)
    public function isAnswerCorrect($answer, $locale = 'en')
    {
        $correctAnswer = $this->getTranslation('correct_answer', $locale);
        if (strtolower(trim($answer)) === strtolower(trim($correctAnswer))) {
            return true;
        }

        $acceptableAnswers = $this->getTranslation('acceptable_answers', $locale);
        if ($acceptableAnswers) {
            $acceptableList = is_array($acceptableAnswers) 
                ? $acceptableAnswers 
                : explode(',', $acceptableAnswers);
            
            foreach ($acceptableList as $acceptable) {
                if (strtolower(trim($answer)) === strtolower(trim($acceptable))) {
                    return true;
                }
            }
        }

        return false;
    }
}

