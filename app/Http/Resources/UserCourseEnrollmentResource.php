<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCourseEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // user object كامل
            'user' => new UserResource($this->user),

            // course object كامل
            'course' => new CourseResource($this->course),

            'enrolled_at' => $this->enrolled_at,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,

            'progress_percentage' => $this->progress_percentage,
            'points_earned' => $this->points_earned,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
