<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Courses\CourseController;
use App\Http\Controllers\Api\Courses\CourseSectionController;
use App\Http\Controllers\Api\Courses\CourseStudentsController;
use App\Http\Controllers\Api\Courses\CourseSurveyController;
use App\Http\Controllers\Api\Courses\CourseTestimonialController;
use App\Http\Controllers\Api\Lessons\CommentController;
use App\Http\Controllers\Api\Lessons\LessonController;
use App\Http\Controllers\Api\Lessons\LessonResourceController;
use App\Http\Controllers\Api\Lessons\QuizController;
use App\Http\Controllers\Api\StudentsController;
use App\Http\Middleware\VerifyAppKey;
use App\Models\Courses\Course;
use Illuminate\Support\Facades\Route;

Route::get('/test/test', function () {
    $course = Course::with('activePromotions')->get();

    return response()->json($course);
});
Route::middleware([VerifyAppKey::class])->group(function () {

    Route::group(['prefix' => '/students'], function () {
        Route::get('/', [StudentsController::class, 'index']);
        Route::group(['prefix' => '/{student}'], function () {
            Route::get('', [StudentsController::class, 'show']);
            Route::get('courses', [StudentsController::class, 'courses']);
        });
    });

    Route::group(['prefix' => '/courses'], function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::post('/{lang}/reorder', [CourseController::class, 'reorder']);

        Route::group(['prefix' => '/{course}'], function () {
            Route::put('', [CourseController::class, 'update']);
            Route::get('', [CourseController::class, 'show']);
            Route::post('cover', [CourseController::class, 'updateCover']);
            Route::put('price', [CourseController::class, 'updatePrice']);
            Route::put('publish', [CourseController::class, 'publish']);
            Route::put('redraft', [CourseController::class, 'redraft']);
            Route::delete('', [CourseController::class, 'delete']);

            Route::get('/students', [CourseStudentsController::class, 'students']);
            Route::group(['prefix' => 'testimonials'], function () {
                Route::get('', [CourseTestimonialController::class, 'index']);
                Route::post('', [CourseTestimonialController::class, 'store']);
                Route::group(['prefix' => '/{testimonial}'], function () {
                    Route::post('upload', [CourseTestimonialController::class, 'upload']);
                    Route::put('', [CourseTestimonialController::class, 'update']);
                    Route::delete('', [CourseTestimonialController::class, 'delete']);
                });
            });

            Route::get('/sections', [CourseSectionController::class, 'index']);
            Route::post('/sections', [CourseSectionController::class, 'store']);
            Route::post('/sections/reorder', [CourseSectionController::class, 'reorder']);
            Route::get('/surveys', [CourseSurveyController::class, 'index']);
            Route::get('/surveys/statistics', [CourseSurveyController::class, 'statistics']);
        });
    });

    Route::group(['prefix' => '/sections'], function () {
        Route::group(['prefix' => '/{section}'], function () {
            Route::group(['prefix' => '/lessons'], function () {
                Route::get('/', [LessonController::class, 'index']);
                Route::post('/', [LessonController::class, 'store']);
                Route::post('/reorder', [LessonController::class, 'reorder']);
            });

            Route::put('', [CourseSectionController::class, 'update']);
            Route::get('', [CourseSectionController::class, 'show']);
            Route::delete('', [CourseSectionController::class, 'delete']);
        });
    });

    Route::group(['prefix' => '/lessons'], function () {
        Route::group(['prefix' => '/{lesson}'], function () {
            Route::get('/', [LessonController::class, 'show']);
            Route::put('/', [LessonController::class, 'update']);

            Route::put('/publish', [LessonController::class, 'publish']);
            Route::put('/redraft', [LessonController::class, 'redraft']);

            Route::delete('/', [LessonController::class, 'delete']);
            Route::get('/comments', [CommentController::class, 'index']);
            Route::post('/comments/{comment}/replies', [CommentController::class, 'reply']);
            Route::delete('/comments/{comment}', [CommentController::class, 'delete']);

            Route::group(['prefix' => '/quiz'], function () {
                Route::get('', [QuizController::class, 'index']);
                Route::post('', [QuizController::class, 'store']);
                Route::post('/reorder', [QuizController::class, 'reorder']);
                Route::put('{quiz}', [QuizController::class, 'update']);
                Route::delete('{quiz}', [QuizController::class, 'delete']);
            });

            Route::group(['prefix' => '/resources'], function () {
                Route::post('', [LessonResourceController::class, 'store']);
                Route::post('{resource}', [LessonResourceController::class, 'update']);
                Route::delete('{resource}', [LessonResourceController::class, 'delete']);
            });
        });
    });

    Route::group(['prefix' => '/categories'], function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'delete']);
    });

    Route::group(['prefix' => '/ar-categories'], function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'delete']);
    });
});

/*
|--------------------------------------------------------------------------
| Mobile API v1 Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/mobile.php';
