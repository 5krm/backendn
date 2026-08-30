<?php

namespace App\Http\Controllers\Api\Courses;

use App\Models\Courses\Course;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CourseStudentsController extends Controller
{
    const FILTER_COMPLETED = 1;
    const FILTER_NOT_STARTED = 2;

    public function students(Course $course): JsonResponse
    {
        $filter = request()->get('filter');

        $students = $course
            ->students()
            ->when(
                $filter == self::FILTER_NOT_STARTED,
                fn($q) => $q->where('progress', 0)
            )
            ->when(
                $filter == self::FILTER_COMPLETED,
                fn($q) => $q->where('progress', 100)
            )
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'avatar' => $student->profile,
                    'started_at' => $student->pivot->created_at->format('M d, Y'),
                    'passed_at' => $student->pivot->passed_at?->format('M d, Y'),
                    'score' => $student->pivot->score,
                    'progress' => $student->pivot->progress,
                ];
            });

        return response()->json($students);
    }
}
