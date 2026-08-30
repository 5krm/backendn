<?php

namespace App\Http\Middleware;

use App\Enums\CourseStatus;
use App\Models\Lessons\Lesson;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLessonAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $lesson = Lesson::query()
            ->with('course')
            ->where('public_key', $request->route('lesson'))
            ->where('status', CourseStatus::published)
            ->first();

        if (! $lesson) {
            return redirect(route('dashboard'));
        }

        /** @var User */
        $user = auth()->user();
        $enrolled = $user->courses()->where('course_id', $lesson->course_id)->exists();
        $isTutor = ($lesson->course->tutor_id == $user->id) ?? false;
        if (! $enrolled && ! $isTutor) {
            return redirect(route('dashboard'));
        }

        return $next($request);
    }
}
