<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionOptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'option_text' => [
                'en' => $this->getTranslation('option_text', 'en'),
                'ar' => $this->getTranslation('option_text', 'ar'),
            ],
            'is_correct' => $this->is_correct,
            'order' => $this->order,
        ];
    }
}

