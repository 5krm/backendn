<?php

namespace App\Http\Middleware;

use App\Models\Courses\Course;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanRateCourse
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $course = is_object($request->route('course')) ? $request->route('course') : Course::where('slug', $request->route('course'))->first();        
        $user = auth()->user();
        $enrollment = $course->students()->find($user->id)?->pivot;
        if (($enrollment?->progress ?? 0) < 100){            
            return abort(403, trans('survey.access_denied', ['link'=>route('app.courses.details', $course)]));
        }
        return $next($request);
    }
}
