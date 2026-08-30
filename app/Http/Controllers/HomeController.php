<?php

namespace App\Http\Controllers;

use App\Http\Controllers\App\Courses\CourseController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {

        return app(CourseController::class)->index($request);
    }
}
