<?php

namespace App\Http\Middleware;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCourseAccess
{
    public function handle(Request $request, Closure $next): Response
    {

        $course = is_object($request->route('course')) ? $request->route('course') : Course::where('slug', $request->route('course'))->first();
        if ($request->routeIs('app.courses.certificate')) {
            $user = auth()->user();

            $enrollment = Enrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->whereNotNull('passed_at')->first();

            if ($enrollment) {
                return $next($request);
            }
        }

        if (! collect([
            CourseStatus::published,
            CourseStatus::preview,
        ])->contains($course?->status)) {
            return redirect()->route('app.courses');
        }

        return $next($request);
    }
}
