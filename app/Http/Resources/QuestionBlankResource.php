<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionBlankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'blank_position' => $this->blank_position,
            'correct_answer' => [
                'en' => $this->getTranslation('correct_answer', 'en'),
                'ar' => $this->getTranslation('correct_answer', 'ar'),
            ],
            'acceptable_answers' => [
                'en' => $this->getTranslation('acceptable_answers', 'en'),
                'ar' => $this->getTranslation('acceptable_answers', 'ar'),
            ],
            'order' => $this->order,
        ];
    }
}

