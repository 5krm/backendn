<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class CourseTestimonialData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Max(255)]
        public string $job_title,
        public string $content
    ) {}
}
