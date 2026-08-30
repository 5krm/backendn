<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;

class CourseData extends Data
{
    public function __construct(
        public string $title,
        public string $description,
        public string $lang,
        public ?string $objectives,
        public ?int $author_id,
        public ?int $category_id,
        public int $order = 1,
    ) {}
}
