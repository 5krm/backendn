<?php

namespace App\Http\Controllers\App\Courses;

use App\Http\Controllers\Controller;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Quizzes\Quiz;
use App\Models\Quizzes\QuizOption;
use App\Models\User;
use App\Services\CourseCompletionService;
use App\Services\FinalExamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public static $QuestionsNo = 20;

    protected FinalExamService $finalExamService;

    protected CourseCompletionService $courseCompletionService;

    public function __construct(FinalExamService $finalExamService, CourseCompletionService $courseCompletionService)
    {
        $this->finalExamService = $finalExamService;
        $this->courseCompletionService = $courseCompletionService;
    }

    public function info(Course $course)
    {
        /** @var User */
        $user = auth()->user();

        // Get exam configuration
        $examConfig = $this->finalExamService->getFinalExamConfig($course);
        $questionsNo = Quiz::whereHas('lesson', fn ($q) => $q->where('course_id', $course->id))->count();

        return view('app.courses.exam.info', [
            'course' => $course,
            'user' => $user,
            'exam_config' => $examConfig,
            'questionsNo' => $questionsNo,
        ]);
    }

    public function access_denied(Course $course)
    {
        /** @var User */
        $user = auth()->user();

        return view('app.courses.exam.403', [
            'course' => $course,
            'user' => $user,
        ]);
    }

    public function get(Course $course)
    {
        /** @var User */
        $user = auth()->user();
        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        $progress = (float) ($enrollment?->progress ?? 0);

        Log::info('Exam access check', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment?->id,
            'progress' => $progress,
            'progress_raw' => $enrollment?->progress,
        ]);

        // Check if user completed all lessons (use >= 99.9 to handle floating point)
        if ($progress < 99.9) {
            Log::info('Access denied - progress not 100%', ['progress' => $progress]);

            return redirect()->route('app.courses.exam.access-denied', ['course' => $course]);
        }

        // Check if course has enough questions for final exam
        $hasEnough = $this->finalExamService->hasEnoughQuestions($course);
        Log::info('Exam questions check', [
            'hasEnoughQuestions' => $hasEnough,
        ]);

        if (! $hasEnough) {
            Log::info('Access denied - not enough questions');

            return redirect()->route('app.courses.exam.access-denied', ['course' => $course])
                ->with('error', 'This course does not have enough quiz questions for a final exam. Please contact the instructor.');
        }

        // Generate final exam questions from lesson quizzes
        $examConfig = $this->finalExamService->getFinalExamConfig($course);
        $questions = $this->finalExamService->generateFinalExam($course, 5); // $examConfig['exam_question_count']);

        // Ensure all questions have options
        $questions = $questions->filter(function ($question) {
            return $question->quizOptions && $question->quizOptions->count() >= 2;
        });

        // If we don't have enough valid questions after filtering, redirect
        if ($questions->count() < 1) {
            Log::info('Access denied - not enough valid questions after filtering', ['count' => $questions->count()]);

            return redirect()->route('app.courses.exam.access-denied', ['course' => $course])
                ->with('error', 'Not enough valid quiz questions available for the final exam.');
        }

        Log::info('Exam ready', ['questions_count' => $questions->count()]);
        // Store question IDs in session to ensure same questions are validated
        session(['exam_questions_'.$course->id => $questions->pluck('id')->toArray()]);

        return view('app.courses.exam.index', [
            'course' => $course,
            'questions' => $questions,
            'user' => $user,
            'exam_config' => $examConfig,
        ]);
    }

    public function save(Request $request, Course $course)
    {
        // Get the question IDs from session to ensure we validate the same questions shown
        $questionIds = session('exam_questions_'.$course->id, []);

        if (empty($questionIds)) {
            return redirect()->route('app.courses.exam', ['course' => $course])
                ->with('error', 'Exam session expired. Please start again.');
        }

        $questions_no = count($questionIds);

        $validator = Validator::make($request->input(), [
            'answers' => ['array', 'required', 'size:'.$questions_no],
            'answers.*' => ['required', 'exists:quiz_options,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Get correct answers only for the questions in this exam session
        $correct_answers = QuizOption::query()
            ->where('is_correct', true)
            ->whereIn('quiz_id', $questionIds)
            ->pluck('id', 'quiz_id');

        $correct = 0;
        foreach ($request->answers as $key => $answer) {
            if ($answer == $correct_answers[$key]) {
                $correct++;
            }
        }

        $score = 100 * $correct / $questions_no;

        // Clear the session after grading
        session()->forget('exam_questions_'.$course->id);

        $certificate = null;
        if ($score >= config('app.passing_score')) {
            $certificate = $this->courseCompletionService->finish_course($course, $score);
        }

        $questions = Quiz::query()
            ->with('quizOptions')
            ->whereIn('id', $questionIds)
            ->get();

        return view('app.courses.exam.result', [
            'user' => auth()->user(),
            'score' => $score,
            'questionsNo' => $questions_no,
            'correctNo' => $correct,
            'course' => $course,
            'certificate' => $certificate,
            'questions' => $questions,
            'answers' => $request->answers,
        ]);
    }
}
