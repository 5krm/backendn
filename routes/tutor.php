<?php

use App\Http\Controllers\Api\Tutor\TutorAssignmentsApiController;
use App\Http\Controllers\Api\Tutor\TutorAuthController;
use App\Http\Controllers\Api\Tutor\TutorCertificatesApiController;
use App\Http\Controllers\Api\Tutor\TutorCommunicationsApiController;
use App\Http\Controllers\Api\Tutor\TutorCoursesApiController;
use App\Http\Controllers\Api\Tutor\TutorDashboardApiController;
use App\Http\Controllers\Api\Tutor\TutorOrganizationsApiController;
use App\Http\Controllers\Api\Tutor\TutorPromotionsApiController;
use App\Http\Controllers\Api\Tutor\TutorReportsApiController;
use App\Http\Controllers\Api\Tutor\TutorStudentsApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tutor API Routes  —  /api/tutor/v1/
|--------------------------------------------------------------------------
*/

Route::name('tutor.v1.')->group(function () {

    // Public Auth
    Route::post('/register', [TutorAuthController::class, 'register'])->name('register');
    Route::post('/login',    [TutorAuthController::class, 'login'])->name('login');

    // Authenticated Tutor Endpoints
    Route::middleware('auth:sanctum')->group(function () {

        // Profile & Settings
        Route::get('/me',                  [TutorAuthController::class, 'me'])->name('me');
        Route::post('/logout',             [TutorAuthController::class, 'logout'])->name('logout');
        Route::put('/profile',             [TutorAuthController::class, 'updateProfile'])->name('update-profile');
        Route::post('/upload-avatar',      [TutorAuthController::class, 'uploadAvatar'])->name('upload-avatar');
        Route::put('/change-password',     [TutorAuthController::class, 'changePassword'])->name('change-password');
        Route::put('/social-links',        [TutorAuthController::class, 'updateSocialLinks'])->name('social-links');
        Route::post('/upload-kyc',         [TutorAuthController::class, 'uploadKyc'])->name('upload-kyc');

        // Dashboard
        Route::get('/dashboard',           [TutorDashboardApiController::class, 'dashboard'])->name('dashboard');

        // Courses Management
        Route::get('/courses',                       [TutorCoursesApiController::class, 'index'])->name('courses.index');
        Route::post('/courses',                      [TutorCoursesApiController::class, 'store'])->name('courses.store');
        Route::get('/courses/{id}',                  [TutorCoursesApiController::class, 'show'])->name('courses.show');
        Route::put('/courses/{course}',              [TutorCoursesApiController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}',           [TutorCoursesApiController::class, 'destroy'])->name('courses.destroy');
        Route::post('/courses/{course}/publish',     [TutorCoursesApiController::class, 'publish'])->name('courses.publish');
        Route::post('/courses/{course}/redraft',     [TutorCoursesApiController::class, 'redraft'])->name('courses.redraft');

        // Sections & Lessons
        Route::post('/courses/{course}/sections',    [TutorCoursesApiController::class, 'storeSection'])->name('sections.store');
        Route::put('/sections/{section}',            [TutorCoursesApiController::class, 'updateSection'])->name('sections.update');
        Route::delete('/sections/{section}',         [TutorCoursesApiController::class, 'deleteSection'])->name('sections.destroy');
        Route::post('/sections/{section}/lessons',   [TutorCoursesApiController::class, 'storeLesson'])->name('lessons.store');
        Route::put('/lessons/{lesson}',              [TutorCoursesApiController::class, 'updateLesson'])->name('lessons.update');
        Route::delete('/lessons/{lesson}',           [TutorCoursesApiController::class, 'deleteLesson'])->name('lessons.destroy');
        Route::post('/lessons/{lesson}/quizzes',     [TutorCoursesApiController::class, 'storeQuiz'])->name('quizzes.store');

        // Assignments & Grading Hub
        Route::get('/assignments',                   [TutorAssignmentsApiController::class, 'index'])->name('assignments.index');
        Route::post('/assignments/{id}/grade',       [TutorAssignmentsApiController::class, 'grade'])->name('assignments.grade');

        // Certificates
        Route::get('/certificates',                  [TutorCertificatesApiController::class, 'index'])->name('certificates.index');
        Route::post('/certificates/templates',       [TutorCertificatesApiController::class, 'saveTemplate'])->name('certificates.save-template');
        Route::post('/certificates/{id}/revoke',     [TutorCertificatesApiController::class, 'revoke'])->name('certificates.revoke');

        // Promotions & Coupons
        Route::get('/promotions',                    [TutorPromotionsApiController::class, 'index'])->name('promotions.index');
        Route::post('/promotions',                   [TutorPromotionsApiController::class, 'store'])->name('promotions.store');
        Route::delete('/promotions/{coupon}',        [TutorPromotionsApiController::class, 'destroy'])->name('promotions.destroy');

        // Students Hub
        Route::get('/students',                      [TutorStudentsApiController::class, 'index'])->name('students.index');
        Route::get('/students/{id}',                 [TutorStudentsApiController::class, 'show'])->name('students.show');
        Route::post('/students/{id}/notes',          [TutorStudentsApiController::class, 'addNote'])->name('students.notes');

        // Reports & Analytics
        Route::get('/reports/courses',               [TutorReportsApiController::class, 'coursesReport'])->name('reports.courses');
        Route::get('/reports/students',              [TutorReportsApiController::class, 'studentsReport'])->name('reports.students');
        Route::get('/reports/quizzes',               [TutorReportsApiController::class, 'quizzesReport'])->name('reports.quizzes');
        Route::get('/reports/earnings',              [TutorReportsApiController::class, 'earningsReport'])->name('reports.earnings');
        Route::post('/reports/payout-request',       [TutorReportsApiController::class, 'requestPayout'])->name('reports.payout-request');

        // Communications & AI Co-Pilot
        Route::get('/messages/comments',             [TutorCommunicationsApiController::class, 'comments'])->name('messages.comments');
        Route::post('/messages/comments/{id}/reply', [TutorCommunicationsApiController::class, 'replyComment'])->name('messages.reply-comment');
        Route::get('/messages/threads',              [TutorCommunicationsApiController::class, 'threads'])->name('messages.threads');
        Route::post('/messages/threads/{id}',        [TutorCommunicationsApiController::class, 'sendMessage'])->name('messages.send');
        Route::post('/messages/broadcast',           [TutorCommunicationsApiController::class, 'broadcast'])->name('messages.broadcast');
        Route::post('/ai/generate',                  [TutorCommunicationsApiController::class, 'aiGenerate'])->name('ai.generate');

        // Organizations & Co-Tutors
        Route::get('/organizations',                 [TutorOrganizationsApiController::class, 'index'])->name('organizations.index');
        Route::post('/organizations/join',           [TutorOrganizationsApiController::class, 'join'])->name('organizations.join');
        Route::post('/organizations/invite',         [TutorOrganizationsApiController::class, 'invite'])->name('organizations.invite');
    });
});
