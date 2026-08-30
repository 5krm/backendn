<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Data;

class LessonCommentData extends Data
{
    public function __construct(
        public string $content,
        public ?int $parent_id,
    ) {}
}
