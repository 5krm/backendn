<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Data;

class LessonNoteData extends Data
{
    public function __construct(
        public ?int $seconds,
        public string $note,
    ) {
    }
}
