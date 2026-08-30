<?php

namespace App\Http\Controllers\Api\Courses;

use Illuminate\Http\Request;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use App\Http\Controllers\Controller;
use App\Models\Courses\CourseSection;
use App\Data\Courses\CourseSectionData;
use App\Http\Resources\Lessons\CourseSectionResource;

class CourseSectionController extends Controller
{
    public function __construct()
    {
        CourseSectionResource::withoutWrapping();
    }

    public function index(Course $course)
    {
        $sections = $course
            ->sections()
            ->withCount(['lessons'])
            ->get();

        return CourseSectionResource::collection($sections);
    }

    public function store(Course $course, CourseSectionData $inputs)
    {
        $section = $course
            ->sections()
            ->create($inputs->toArray());

        return CourseSectionResource::make($section->load('lessons'));
    }

    public function update(CourseSection $section, CourseSectionData $data)
    {
        $section->update($data->toArray());
        return CourseSectionResource::make($section->load('lessons'));
    }

    public function reorder(Course $course, Request $request)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'integer'],
            'data.*.index' => ['required', 'integer'],
        ]);

        $course->load(['sections.lessons']);

        $data = collect($data['data'])->keyBy('id')->toArray();
        $course
            ->sections
            ->each(function (CourseSection $section) use ($data) {
                if ($section->update(['order' => $data[$section->id]['index']])) {
                    $section->lessons->each(fn(Lesson $lesson) =>  $lesson->update(['section_order' => $section->order]));
                }
            });

        $result = $course->sections->sortBy('order')->values();
        return CourseSectionResource::collection($result);
    }

    public function delete(CourseSection $section)
    {
        $section->lessons()->delete();
        $section->delete();

        return response()->noContent();
    }
}
