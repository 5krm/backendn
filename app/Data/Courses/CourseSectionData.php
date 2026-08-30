<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;

class CourseSectionData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        public string $description,
        public int $order = 1,
    ) {
    }
}
