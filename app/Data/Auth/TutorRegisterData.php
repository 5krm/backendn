<?php

namespace App\Data\Auth;

use App\Models\User;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class TutorRegisterData extends Data
{
	public function __construct(
		#[Max(255)]
		public string $name,
		#[Max(255), Email(), Unique(User::class)]
		public string $email,
		#[Password(default: true), Confirmed()]
		public string $password,
	) {
	}
}