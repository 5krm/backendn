<?php

namespace App\ViewModels\Lessons;

use App\Models\Lessons\Lesson;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;

class LessonView implements Arrayable
{
    public ?Pivot $tracking = null;

    public bool $prevCompleted = false;

    public function __construct(public Lesson $lesson, public Collection $trackings)
    {
        $orderedTrackings = $trackings->values();

        $this->tracking = $orderedTrackings->firstWhere('lesson_id', $this->lesson->id);

        $index = $orderedTrackings->search(
            fn ($tracking) => (int) $tracking->lesson_id === (int) $this->lesson->id
        );

        if ($index !== false && $index > 0) {
            $this->prevCompleted = $orderedTrackings[$index - 1]->completed_at != null;
        }
    }

    public function toArray(): array
    {
        $this->lesson->loadMissing('courseSection');

        return [
            'public_key' => $this->lesson->public_key,
            'title' => $this->lesson->title,
            'duration' => $this->lesson->textDuration,
            'completed_at' => $this->tracking?->completed_at,
            'section_id' => $this->lesson->section_id,
            'order' => $this->lesson->order,
            'prev_completed' => $this->prevCompleted,
        ];
    }
}
