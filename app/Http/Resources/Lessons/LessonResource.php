<?php

namespace App\Http\Resources\Lessons;

use App\Enums\CourseStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => route('app.lessons.lesson', $this),
            'video_id' => $this->video_id,
            'video_html' => $this->video_html,
            'duration' => $this->duration ?? 0,
            'formatted_duration' => $this->textDuration,
            'content' => $this->content,
            'order' => $this->order,
            'course_id' => $this->course_id,
            'section_id' => $this->section_id,
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'resources' => LessonResourceResource::collection($this->whenLoaded('resources')),
            'quizzes' => QuizResource::collection($this->whenLoaded('quizzes')),
            'status' => [
                'key' => $this->status,
                'value' => CourseStatus::titles()[$this->status->value]
            ],
        ];
    }
}
