<?php

namespace App\Data\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
	public function __construct(
		#[Max(255)]
		public string $name,
		#[Max(255), Email(Email::FilterEmailValidation)]
		public string $email,
		#[Password(default: true), Confirmed()]
		public string $password,
		#[Max(50)]
		public string $phone,
		public int $country_id,
	) {
	}

	public static function rules(): array
	{
		return [
			'email' => [
				'required',
				'string',
				'email:filter',
				'max:255',
				'regex:/^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/',
				Rule::unique(User::class)->whereNull('deleted_at'),
			],
		];
	}

	public static function messages(): array
	{
		return [
			'email.regex' => __('validation.email'),
		];
	}
}
