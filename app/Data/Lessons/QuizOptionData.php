<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Data;

class QuizOptionData extends Data
{
	public function __construct(
		public int $order,
		public string $value,
		public bool $is_correct
	) {
	}
}
