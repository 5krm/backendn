<?php

namespace App\Data\Courses;

use Spatie\LaravelData\Data;
use App\Data\Courses;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class ExamData extends Data
{
    public function __construct(
        #[DataCollectionOf(AnswerData::class)]
        public DataCollection $answers,
    ) {
    }
}

class AnswerData extends Data
{
    public function __construct(
        public int $question,
        public string $answer
    ) {
    }
}
