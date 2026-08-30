<?php

namespace App\Http\Resources\Lessons;

use App\Enums\FileType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'title' => $this->title,
            'file_type' => [
                'key' => $this->file_type,
                'value' => FileType::names()[$this->file_type->value]
            ],
            'file' => $this->file,
        ];
    }
}
