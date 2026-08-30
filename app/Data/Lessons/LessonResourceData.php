<?php

namespace App\Data\Lessons;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\MimeTypes;

class LessonResourceData extends Data
{
    public function __construct(
        #[Max(255)]
        public string $title,
        public UploadedFile $file
    ) {}
}
