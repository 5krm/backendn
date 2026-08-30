<?php

namespace App\Data\Profile;

use App\Enums\SocialPlatform;
use Spatie\LaravelData\Data;
use Illuminate\Validation\Rule;

class UpdateProfileData extends Data
{
	public function __construct(
		public string $name,
		public string $email,
		public string $phone,
		public int $country_id,
		public ?string $job_title = null,
		public ?string $job_title_en = null,
		public ?string $bio = null,
		public ?string $bio_en = null,
		/** @var array<int, array{platform?: string, url?: string}>|null */
		public ?array $social_links = null,
	) {}

	public static function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'email', Rule::unique('users')->ignore(auth()->id())],
			'phone' => ['required', 'string', 'max:50'],
			'country_id' => ['required', 'integer', 'exists:countries,id'],
			'job_title' => ['nullable', 'string', 'max:255'],
			'job_title_en' => ['nullable', 'string', 'max:255'],
			'bio' => ['nullable', 'string', 'max:5000'],
			'bio_en' => ['nullable', 'string', 'max:5000'],
			'social_links' => ['nullable', 'array'],
			'social_links.*.platform' => ['required_with:social_links.*.url', Rule::in(SocialPlatform::values())],
			'social_links.*.url' => ['nullable', 'url', 'max:255'],
		];
	}
}
