<?php

namespace App\Http\Resources\Lessons;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'duration' => $this->duration ?? 0,
            'formatted_duration' => $this->textDuration,
            'lesson_count' => $this->lessons_count ?? 0,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'lessons' => LessonResource::collection($this->whenLoaded('lessons'))
        ];
    }
}
