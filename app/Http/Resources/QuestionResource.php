<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'type' => $this->type,
            'question_text' => [
                'en' => $this->getTranslation('question_text', 'en'),
                'ar' => $this->getTranslation('question_text', 'ar'),
            ],
            'explanation' => [
                'en' => $this->getTranslation('explanation', 'en'),
                'ar' => $this->getTranslation('explanation', 'ar'),
            ],
            'image' => $this->image,
            'points' => $this->points,
            'order' => $this->order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add type-specific data
        if (in_array($this->type, ['multiple_choice', 'single_choice'])) {
            $data['options'] = QuestionOptionResource::collection($this->whenLoaded('options'));
        }

        if ($this->type === 'matching') {
            $data['matches'] = QuestionMatchResource::collection($this->whenLoaded('matches'));
        }

        if ($this->type === 'complete') {
            $data['blanks'] = QuestionBlankResource::collection($this->whenLoaded('blanks'));
        }

        return $data;
    }
}

