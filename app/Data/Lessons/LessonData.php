<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;

class LessonData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        public ?string $content,
        public ?string $video_id,

        #[MapInputName('order')]
        public int $lesson_order = 1,
    ) {
    }
}
