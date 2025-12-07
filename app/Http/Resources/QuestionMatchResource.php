<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionMatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'left_side' => [
                'en' => $this->getTranslation('left_side', 'en'),
                'ar' => $this->getTranslation('left_side', 'ar'),
            ],
            'right_side' => [
                'en' => $this->getTranslation('right_side', 'en'),
                'ar' => $this->getTranslation('right_side', 'ar'),
            ],
            'order' => $this->order,
        ];
    }
}

