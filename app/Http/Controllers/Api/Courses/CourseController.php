<?php

namespace App\Http\Controllers\Api\Courses;

use App\Enums\CourseStatus;
use Illuminate\Http\Request;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use App\Data\Courses\PriceData;
use App\Events\CoursePublished;
use App\Data\Courses\CourseData;
use App\Http\Controllers\Controller;
use App\Models\Courses\CourseSection;
use App\Http\Resources\Courses\CourseResource;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    public function __construct()
    {
        CourseResource::withoutWrapping();
    }

    public function index()
    {
        $courses = Course::query()
            ->with(['tutor', 'category'])
            ->withCount(['lessons', 'students'])
            ->get();

        return CourseResource::collection($courses);
    }

    public function store(CourseData $inputs)
    {
        $course = Course::create($inputs->toArray());
        $course->status = CourseStatus::draft;
        $course->organization_id = auth()->user()->organization_id;

        return CourseResource::make($course->load(['tutor', 'category']));
    }

    public function show(Course $course)
    {
        $course
            ->load(['testimonials', 'tutor', 'category'])
            ->loadCount(['lessons', 'students']);

        return CourseResource::make($course);
    }

    public function update(Course $course, CourseData $data)
    {
        $course->update($data->toArray());
        return CourseResource::make($course->load(['sections.lessons', 'tutor', 'category']));
    }

    public function updatePrice(Course $course, PriceData $data)
    {
        $course->update($data->toArray());
        return CourseResource::make($course->load(['sections.lessons', 'tutor', 'category']));
    }

    public function reorder(string $lang, Request $request)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'integer'],
            'data.*.index' => ['required', 'integer'],
        ]);

        $data = collect($data['data'])->keyBy('id')->toArray();
        $courses = Course::query()
            ->where('lang', $lang)
            ->get();

        $courses->each(fn(Course $course) => $course->update(['order' => $data[$course->id]['index']]));
        return CourseResource::collection($courses);
    }

    public function updateCover(Course $course, Request $request)
    {
        $file = $request->file('cover_image');
        $course->clearMediaCollection('covers');
        $course->addMedia($file)->toMediaCollection('covers');

        return CourseResource::make($course->load(['sections.lessons', 'tutor', 'category']));
    }

    public function publish(Course $course)
    {
        $warnings = [];
        if (!CourseSection::where('course_id', $course->id)->exists()) {
            $warnings[] = 'This course should contain at least one section.';
        }
        if (!Lesson::where(['course_id' => $course->id, 'status' => CourseStatus::published])->exists()) {
            $warnings[] = 'This course should contain at least one published lesson.';
        }
        if (is_null($course->stripe_price_id) && $course->is_free == false) {
            $warnings[] = 'This course should have a price.';
        }

        if (count($warnings) > 0) {
            throw ValidationException::withMessages($warnings);
        }

        $course->status = CourseStatus::published;
        $course->save();

        event(new CoursePublished($course));

        return CourseResource::make($course->load(['tutor', 'category']));
    }

    public function redraft(Course $course)
    {
        $course->status = CourseStatus::draft;
        $course->save();

        return CourseResource::make($course->load(['tutor', 'category']));
    }


    public function delete(Course $course)
    {
        $course->sections()->delete();
        $course->delete();

        return response()->noContent();
    }
}
