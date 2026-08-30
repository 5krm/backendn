<?php

namespace App\Data\Profile;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Confirmed;

class ChangePasswordData extends Data
{
	public function __construct(
		public string $current_password,
		#[Password(default: true), Confirmed()]
		public string $new_password,
	) {
	}
}
