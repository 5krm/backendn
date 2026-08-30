<?php

namespace App\Http\Resources;

use App\Enums\SatisfactionCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseSurveyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'username' => $this->user->name,
            'satisfaction' => ['key' => $this->satisfaction, 'value' => SatisfactionCase::names()[$this->satisfaction]],
            'comment' => $this->comment,
            'as_expected' => $this->as_expected,
            'suggestions' => $this->suggestions,
            'created_at' => $this->created_at->format('M d, Y'),
        ];
    }
}
