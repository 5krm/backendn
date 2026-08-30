<?php

namespace App\Data\Profile;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Password;
use Spatie\LaravelData\Data;

class ChangePasswordData extends Data
{
    public function __construct(
        public string $current_password,
        #[Password(default: true), Confirmed()]
        public string $new_password,
    ) {}
}
