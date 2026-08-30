<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class LoginData extends Data
{
	public function __construct(
		#[Email()]
		public string $email,
		public string $password,
	) {
	}
}
