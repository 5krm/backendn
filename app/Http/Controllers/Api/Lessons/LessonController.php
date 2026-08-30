<?php

namespace App\Http\Controllers\Api\Lessons;

use App\Enums\CourseStatus;
use Illuminate\Support\Str;
use App\Enums\PreferenceKey;
use Illuminate\Http\Request;
use App\Mail\PublishedLesson;
use App\Models\Lessons\Lesson;
use App\Data\Lessons\LessonData;
use App\Actions\UpdateLessonAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Models\Courses\CourseSection;
use App\Http\Resources\Lessons\LessonResource;

class LessonController extends Controller
{
    public function __construct()
    {
        LessonResource::withoutWrapping();
    }

    public function index(CourseSection $section)
    {
        $lessons = $section->lessons()->get();
        return LessonResource::collection($lessons);
    }

    public function show(Lesson $lesson)
    {
        $lesson = $lesson->load([
            'course',
            'resources',
            'quizzes.quizOptions'
        ]);

        return LessonResource::make($lesson);
    }

    public function store(CourseSection $section, LessonData $inputs)
    {
        $data = $inputs->except('video_id')->toArray();
        $data['public_key'] = Str::uuid();
        $data['course_id'] = $section->course_id;
        $data['section_order'] = $section->order;
        $data['lesson_order'] = $section->lessons()->count() + 1;
        $lesson = $section->lessons()->create($data);

        return LessonResource::make($lesson->fresh());
    }

    public function update(Lesson $lesson, LessonData $data)
    {
        $lesson = (new UpdateLessonAction())->execute($lesson, $data);
        if (!$lesson) {
            return LessonResource::make($lesson)->additional([
                'error' => 'Video was not found!'
            ]);
        }

        return LessonResource::make($lesson);
    }

    public function publish(Lesson $lesson)
    {
        $lesson->load(['quizzes']);

        $warnings = [];
        if ($lesson->quizzes->count() == 0) {
            $warnings[] = 'This lesson should contain at least one quiz.';
        }
        if (!isset($lesson->content)) {
            $warnings[] = 'This lesson should contain a description content.';
        }
        if (!isset($lesson->video_id)) {
            $warnings[] = 'This lesson should contain a video.';
        }
        if (count($warnings) > 0) {
            return response([
                'errors' => $warnings
            ], 422);
        }

        $lesson->status = CourseStatus::published;
        $lesson->save();

        $this->notifyUsers($lesson);

        return LessonResource::make($lesson);
    }

    public function redraft(Lesson $lesson)
    {
        $lesson->status = CourseStatus::draft;
        $lesson->save();

        return LessonResource::make($lesson);
    }

    private function notifyUsers(Lesson $lesson)
    {
        $lesson->load(['course.students' => function ($q) {
            $q->whereHas('preferences', function ($query) {
                return $query
                    ->where('key', PreferenceKey::UpdateEmail)
                    ->where('value', true);
            });
        }, 'courseSection']);

        $students = $lesson->course->students;
        foreach ($students as $student) {
            Mail::to($student)->send(new PublishedLesson($student, $lesson));
        }
    }

    public function reorder(CourseSection $section, Request $request)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
            'data.*.id' => ['required', 'integer'],
            'data.*.index' => ['required', 'integer'],
        ]);

        $section->load(['lessons']);

        $data = collect($data['data'])->keyBy('id')->toArray();
        $section
            ->lessons
            ->each(fn(Lesson $lesson) => $lesson->update(['lesson_order' => $data[$lesson->id]['index']]));

        $result = $section->lessons->sortBy('order')->values();
        return LessonResource::collection($result);
    }

    public function delete(Lesson $lesson)
    {
        $lesson->resources()->delete();
        $lesson->delete();

        return response()->noContent();
    }
}
