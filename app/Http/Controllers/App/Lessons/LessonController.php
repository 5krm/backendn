<?php

namespace App\Http\Controllers\App\Lessons;

use App\Enums\CourseStatus;
use App\Events\LessonTrackingEvent;
use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use App\Models\Quizzes\Quiz;
use App\Models\User;
use App\Services\CourseCompletionService;
use App\ViewModels\Courses\CourseView;
use App\ViewModels\Lessons\LessonDetailsView;
use App\ViewModels\Lessons\LessonView;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    public function index(Course $course)
    {
        // Try to get the last tracked lesson
        $lastLesson = LessonTracking::with('lesson')
            ->where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereHas('lesson', fn ($q) => $q->where('status', CourseStatus::published))
            ->orderBy('created_at', 'desc')
            ->first()?->lesson;

        // If no tracked lesson, try to get the first published lesson
        if (! $lastLesson) {
            $lastLesson = Lesson::where('course_id', $course->id)
                ->where('status', CourseStatus::published)
                ->orderBy('order')
                ->first();
        }

        // If still no lesson found, redirect to course details with a message
        if (! $lastLesson) {
            return redirect()
                ->route('app.courses.details', $course->slug)
                ->with('error', 'This course has no lessons yet. Please check back later.');
        }

        return redirect(route('app.lessons.lesson', $lastLesson->public_key));
    }

    public function markComplete(string $key)
    {
        /** @var Lesson */
        $lesson = Lesson::query()
            ->withCount('resources')
            ->with(['course.surveys'])
            ->where('public_key', $key)
            ->firstOrFail();

        /** @var User */
        $user = auth()->user();

        if (! $lesson->completed($user->id)) {
            if ($lesson->quizzes()->exists()) {
                return redirect(route('app.lessons.lesson', $lesson?->public_key));
            }

            event(new LessonTrackingEvent($lesson));
        }

        $courseHasExam = Quiz::query()
            ->whereHas('lesson', fn ($q) => $q->where('course_id', $lesson->course->id))
            ->exists();

        if ($courseHasExam) {
            return redirect(route('app.courses.exam-info', ['course' => $lesson->course->slug]));
        } else {
            (new CourseCompletionService)->finish_course($lesson->course, 100);
        }

        return redirect(route('app.courses.details', $lesson->course->slug));
    }

    public function show(string $key)
    {
        /** @var Lesson */
        $lesson = Lesson::query()
            ->withCount('resources')
            ->with(['course.surveys'])
            ->where('public_key', $key)
            ->firstOrFail();

        /** @var User */
        $user = auth()->user();

        $previousLesson = $lesson->previous();
        if ($previousLesson && ! $previousLesson->completed($user->id)) {
            if ($previousLesson->quizzes()->exists() || ! $previousLesson->trackings()->where('user_id', $user->id)->exists()) {
                return redirect(route('app.lessons.lesson', $previousLesson?->public_key));
            }

            event(new LessonTrackingEvent($previousLesson));
        }

        if (! $user->lessons()->where('public_key', $key)->exists()) {
            $user->lessons()->attach($lesson, ['course_id' => $lesson->course_id]);
        }

        $trackings = $user->completedLessons($lesson->course_id);
        $lessonModel = (new LessonDetailsView($lesson, $lesson->course?->slug, $trackings))->toArray();

        $course = $lesson->course;
        $course->load(['sections' => fn ($q) => $q->whereHas('publishedLessons'), 'sections.publishedLessons'])->loadCount('publishedLessons');

        $enrollment = Enrollment::query()
            ->where('course_id', $lesson->course_id)
            ->where('user_id', Auth::id())
            ->first();

        $sections = $course->sections->map(fn ($sec) => array_merge($sec->toArray(), [
            'textDuration' => $sec->textDuration,
            'lessons' => $sec->publishedLessons->map(fn ($lesson) => (new LessonView($lesson, $trackings))->toArray()),
        ]));

        $courseModel = (new CourseView($course))->toArray();
        $courseModel['has_quizzes'] = Quiz::whereHas('lesson', fn ($q) => $q->where('course_id', $course->id))->exists();

        return view('app.courses.lesson', [
            'enrollment' => $enrollment,
            'course' => $courseModel,
            'lesson' => $lessonModel,
            'sections' => $sections,
        ]);
    }
}
