<?php

namespace App\Data\Authors;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

class AuthorData extends Data
{
    public function __construct(
        public string $name,
        public string $job_title,
        public ?string $bio,
        public ?UploadedFile $image
    ) {}
}
