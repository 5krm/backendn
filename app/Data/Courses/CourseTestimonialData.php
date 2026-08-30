<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Max;

class CourseTestimonialData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $name,
        #[Max(255)]
        public string $job_title,
        public string $content
    ) {
    }
}
