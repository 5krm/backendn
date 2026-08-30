<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Courses\Enrollment;
use App\Http\Resources\Students\StudentCoursesResource;
use App\Http\Resources\Students\StudentResource;



class StudentsController extends Controller
{

    public function index()
    {
        $Users = User::withCount('courses')->get();

        return response()->json([
            'data' => $Users
        ]);
    }

    public function show(User $student)
    {
        $data = $student->load(['media'])->loadCount(['notes', 'comments']);
        return StudentResource::make($data);
    }
    public function courses(User $student)
    {
        $courses = $student->load('courses')->courses;
        return StudentCoursesResource::collection($courses);
    }
}
