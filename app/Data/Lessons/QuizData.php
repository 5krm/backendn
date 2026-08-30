<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class QuizData extends Data
{
    public function __construct(
        public string $question,

        #[DataCollectionOf(QuizOptionData::class)]
        public DataCollection $options,
    ) {}
}
