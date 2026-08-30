<?php

namespace App\Http\Resources\Students;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentCoursesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'objectives' => $this->objectives,
            'lang' => $this->lang,
            'cover_image' => $this->coverImage,
            'formatted_duration' => $this->textDuration,
            'price' => $this->price,
            'old_price' => $this->old_price,
            'started_at' => $this->pivot->created_at?->format('M d, Y'),
            'passed_at' => $this->pivot->passed_at?->format('M d, Y'),
            'lesson_count' => $this->lessons_count ?? 0,
            'score' => $this->pivot->score,
            'progress' => $this->pivot->progress,
        ];
    }
}
