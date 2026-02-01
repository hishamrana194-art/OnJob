<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleProgress extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
        "id"=>$this->id,
        "user"=>new UserResource($this->user),
        "course"=>new CourseResource($this->course),
        "module"=>new ModuleResource($this->module),
        "is_completed"=>$this->is_completed,
        "progress_percentage"=>$this->progress_percentage,
        "time_spent_seconds"=>$this->time_spent_seconds

        ];
    }
}
