<?php

namespace App\Http\Controllers\App\Lessons;

use App\Http\Controllers\Controller;
use App\Models\Lessons\LessonResource;
use Exception;
use Illuminate\Support\Facades\Storage;

class LessonResourceDownloadController extends Controller
{
    public function __invoke(LessonResource $resource)
    {
        try {
            $media = $resource->getLastMedia('resources');
            abort_if(! $media, 404, 'File not found');

            // clean safe filename
            $filename = preg_replace('/[^A-Za-z0-9\- ]/', '', $resource->title)
                .'.'
                .$media->extension;

            return Storage::download(
                $media->getPath(),
                $filename
            );
        } catch (Exception $e) {
            abort(404, 'File not found');
        }
    }
}
