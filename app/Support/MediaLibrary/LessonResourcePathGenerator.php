<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class LessonResourcePathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $courseId = $media->getCustomProperty('course_id', 'temp');

        return "courses/{$courseId}/lessons/";
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media).'conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'responsive/';
    }
}
