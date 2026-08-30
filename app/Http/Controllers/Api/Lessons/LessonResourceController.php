<?php

namespace App\Http\Controllers\Api\Lessons;

use App\Enums\FileType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Lessons\Lesson;
use App\Http\Controllers\Controller;
use App\Models\Lessons\LessonResource;
use App\Data\Lessons\LessonResourceData;
use App\Http\Resources\Lessons\LessonResourceResource;

class LessonResourceController extends Controller
{

    public function __construct()
    {
        LessonResourceResource::withoutWrapping();
    }

    public function store(Lesson $lesson, LessonResourceData $inputs)
    {
        $data = $inputs->except('file')->toArray();
        $data['public_key'] = Str::uuid();
        $data['file_type'] = FileType::fromMimeType($inputs->file->getMimeType());
        $resource = $lesson->resources()->create($data);
        $resource->addMedia($inputs->file)->toMediaCollection('resources');

        return LessonResourceResource::make($resource);
    }

    public function update(int $lesson, LessonResource $resource, Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $resource->title = $request->title;
        if ($request->hasFile('file')) {
            $resource->clearMediaCollection('resources');
            $resource->file_type = FileType::fromMimeType($request->file->getMimeType());
            $resource->addMedia($request->file)->toMediaCollection('resources');
        }

        $resource->save();

        return LessonResourceResource::make($resource);
    }

    public function delete(int $lesson, LessonResource $resource)
    {
        $resource->clearMediaCollection('resources');
        $resource->delete();

        return response()->noContent();
    }
}
