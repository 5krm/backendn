<?php

namespace App\Http\Controllers\Api\Courses;

use App\Enums\SatisfactionCase;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseSurveyResource;
use App\Models\Courses\Course;

class CourseSurveyController extends Controller
{
    public function __construct()
    {
        CourseSurveyResource::withoutWrapping();
    }

    public function index(Course $course)
    {
        $surveys = $course->surveys()->get();

        return CourseSurveyResource::collection($surveys);
    }

    public function statistics(Course $course)
    {
        $course->load('surveys');

        $statistics = [];
        foreach (SatisfactionCase::values() as $status) {
            $statistics[] = [
                'key' => $status,
                'status' => SatisfactionCase::names()[$status],
                'total' => $course->surveys->where('satisfaction', $status)->count(),
            ];
        }

        return response()->json($statistics);
    }
}
