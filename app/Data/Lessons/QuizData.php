<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Data;
use App\Data\Lessons\QuizOptionData;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class QuizData extends Data
{
	public function __construct(
		public string $question,

		#[DataCollectionOf(QuizOptionData::class)]
		public DataCollection $options,
	) {
	}
}
