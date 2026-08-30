<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;

class SurveyData extends Data
{
    public function __construct(
        public string $satisfaction,
        public string $comment,
        public string $as_expected,
        public ?array $suggestions,
    ) {
    }
}
