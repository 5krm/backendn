<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class LessonData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        public ?string $content,
        public ?string $video_id,

        #[MapInputName('order')]
        public int $lesson_order = 1,
    ) {}
}
