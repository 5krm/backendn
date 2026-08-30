<?php

namespace App\Http\Controllers\App;

use Exception;
use App\Data\Courses\SurveyData;
use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\User;

class SurveyController extends Controller
{
    public function index(Course $course)
    {
        /** @var User */
        $user = auth()->user();
        $enrollment = $course->students()->find($user->id)?->pivot;
        if (($enrollment?->progress ?? 0) < 100){            
            return abort(403, trans('survey.access_denied', ['link'=>route('app.courses.details', $course)]));
        }

        if ($course->whereHas('surveys', fn ($q) => $q->where('user_id', $user->id))->exists()) {
            return response(abort(403, trans('survey.already_done')))->withException(new Exception('You have already completed this survey.'));
        }
        return view('app.survey', [
            'user' => $user,
            'course' => $course,
        ]);
    }

    public function store(Course $course, SurveyData $inputs)
    {
        $data = $inputs->toArray();
        $data['course_id'] = $course->id;
        $data['user_id'] = auth()->user()->id;
        $course->surveys()->create($data);
        return redirect()->route('app.courses.details', $course)->with('survey_complete', 'Thank you for your feedback!');
    }
}
