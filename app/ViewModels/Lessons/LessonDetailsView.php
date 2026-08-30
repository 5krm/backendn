<?php

namespace App\ViewModels\Lessons;

use App\Models\Lessons\Lesson;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonDetailsView implements Arrayable
{
    public string $courseSlug;

    public ?Pivot $tracking;

    public function __construct(public Lesson $lesson, string $courseSlug, $trackings)
    {
        $this->courseSlug = $courseSlug;
        $this->tracking = $trackings->where('lesson_id', $this->lesson->id)->first();
    }

    public function toArray(): array
    {
        return [
            'lesson_id' => $this->lesson->id,
            'course_slug' => $this->courseSlug,
            'public_key' => $this->lesson->public_key,
            'order' => $this->lesson->order,
            'video' => $this->lesson->video_html,
            'video_id' => $this->lesson->getVideoId(),
            'title' => $this->lesson->title,
            'duration' => $this->lesson->textDuration,
            'content' => $this->lesson->content,
            'completed_at' => $this->tracking?->completed_at,
            'section_id' => $this->lesson->section_id,
            'has_quiz' => $this->lesson->quizzes()->exists(),
            'next' => $this->lesson->next(),
            'previous' => $this->lesson->previous(),
            'resources_count' => $this->lesson->resources_count,
        ];
    }
}
