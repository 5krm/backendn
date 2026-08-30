<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class ExamData extends Data
{
    public function __construct(
        #[DataCollectionOf(AnswerData::class)]
        public DataCollection $answers,
    ) {}
}

class AnswerData extends Data
{
    public function __construct(
        public int $question,
        public string $answer
    ) {}
}
