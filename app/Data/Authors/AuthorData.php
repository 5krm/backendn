<?php

namespace App\Data\Authors;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class AuthorData extends Data
{
    public function __construct(
        public string $name,
        public string $job_title,
        public ?string $bio,
        public ?UploadedFile $image
    ) {}
}
