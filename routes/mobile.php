<?php

use App\Http\Controllers\Api\Mobile\Auth\MobileAuthController;
use App\Http\Controllers\Api\Mobile\Student\MobileStudentCourseController;
use App\Http\Controllers\Api\Mobile\Student\MobileEnrollmentController;
use App\Http\Controllers\Api\Mobile\Student\MobileProgressController;
use App\Http\Controllers\Api\Mobile\Student\MobileQuizController;
use App\Http\Controllers\Api\Mobile\Student\MobileWishlistController;
use App\Http\Controllers\Api\Mobile\Student\MobileNotificationController;
use App\Http\Controllers\Api\Mobile\Student\MobileCourseRatingController;
use App\Http\Controllers\Api\Mobile\Student\MobileNotesController;
use App\Http\Controllers\Api\Mobile\Student\MobileCommentsController;
use App\Http\Controllers\Api\Mobile\Tutor\MobileTutorDashboardController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API Routes  —  /api/mobile/v1/
|--------------------------------------------------------------------------
|
| All routes for the NGO Academy Flutter mobile application.
| Authentication uses Laravel Sanctum personal access tokens.
|
| DO NOT modify routes/api.php — those serve the existing web/Filament app.
|
*/

Route::prefix('mobile/v1')->name('mobile.')->middleware(['check_api_version', 'api_locale', 'throttle:mobile'])->group(function () {

    Route::get('/health', function () {
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbStatus = 'OK';
        } catch (\Exception $e) {
            $dbStatus = 'Error: ' . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'System is healthy',
            'data' => [
                'status' => 'OK',
                'database' => $dbStatus,
                'timestamp' => now()->toIso8601String(),
                'environment' => app()->environment(),
            ]
        ]);
    });

    // ── Public (no auth required) ──────────────────────────────────────────

    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register',        [MobileAuthController::class, 'register'])->name('register');
        Route::post('login',           [MobileAuthController::class, 'login'])->name('login');
        Route::post('forgot-password', [MobileAuthController::class, 'forgotPassword'])->name('forgot-password');
        Route::post('reset-password',  [MobileAuthController::class, 'resetPassword'])->name('reset-password');
    });

    // Public course browsing
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/',          [MobileStudentCourseController::class, 'index'])->name('index');
        Route::get('featured',   [MobileStudentCourseController::class, 'featured'])->name('featured');
        Route::get('search',     [MobileStudentCourseController::class, 'search'])->name('search');
        Route::get('suggestions', [MobileStudentCourseController::class, 'suggestions'])->name('suggestions');
        Route::get('recommended', [MobileStudentCourseController::class, 'recommended'])->name('recommended');
        Route::get('{course}/reviews', [MobileCourseRatingController::class, 'index'])->name('reviews');
        Route::get('{course}/ratings', [MobileCourseRatingController::class, 'index'])->name('ratings');
        Route::get('{slug}',     [MobileStudentCourseController::class, 'show'])->name('show');
    });

    Route::get('categories', [MobileStudentCourseController::class, 'categories'])->name('categories');

    // Certificates verification
    Route::post('certificates/verify', [\App\Http\Controllers\Api\Mobile\MobileCertificateController::class, 'verify'])->name('certificates.verify');

    // Tutor public profile
    Route::get('tutors/{id}', [\App\Http\Controllers\Api\Mobile\Tutor\MobileTutorProfileController::class, 'show'])->name('tutors.show');

    // Extra Public routes
    Route::get('feeds', [FeedController::class, 'index'])->name('feeds.index');
    Route::get('pages/{slug}', [StaticPageController::class, 'show'])->name('pages.show');

    // ── Authenticated ──────────────────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('achievements', [AchievementController::class, 'index'])->name('achievements.index');
        Route::post('coupons/verify', [CouponController::class, 'verify'])->name('coupons.verify');

        // Auth management
        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('logout',         [MobileAuthController::class, 'logout'])->name('logout');
            Route::post('refresh',        [MobileAuthController::class, 'refresh'])->name('refresh');
            Route::get('me',              [MobileAuthController::class, 'me'])->name('me');
            Route::put('me',              [MobileAuthController::class, 'updateProfile'])->name('update-profile');
            Route::post('avatar',         [MobileAuthController::class, 'uploadAvatar'])->name('upload-avatar');
            Route::post('fcm-token',      [MobileAuthController::class, 'saveFcmToken'])->name('fcm-token');
            Route::delete('delete-account',[MobileAuthController::class, 'deleteAccount'])->name('delete-account');
        });

        // ── Student routes ─────────────────────────────────────────────────

        // Enrollment & In-App Purchases
        Route::post('courses/{course}/enroll',   [MobileEnrollmentController::class, 'enroll'])->name('enroll');
        Route::post('courses/{course}/purchase', [MobileEnrollmentController::class, 'purchase'])->name('purchase');

        // My enrolled courses
        Route::prefix('student')->name('student.')->group(function () {
            Route::get('courses',              [MobileEnrollmentController::class, 'myEnrollments'])->name('courses');
            Route::get('courses/{enrollment}', [MobileEnrollmentController::class, 'enrollmentDetail'])->name('enrollment-detail');
            Route::get('dashboard',            [MobileEnrollmentController::class, 'dashboard'])->name('dashboard');
        });

        // Lessons (enrollment-gated)
        Route::prefix('lessons/{lesson}')->name('lessons.')->group(function () {
            Route::get('/',          [MobileProgressController::class, 'showLesson'])->name('show');
            Route::post('complete',  [MobileProgressController::class, 'completeLesson'])->name('complete');
            Route::post('progress',  [MobileProgressController::class, 'updateProgress'])->name('progress');

            // Notes
            Route::get('notes', [MobileNotesController::class, 'index'])->name('notes.index');
            Route::post('notes', [MobileNotesController::class, 'store'])->name('notes.store');
            Route::put('notes/{note}', [MobileNotesController::class, 'update'])->name('notes.update');
            Route::delete('notes/{note}', [MobileNotesController::class, 'destroy'])->name('notes.destroy');

            // Comments
            Route::get('comments', [MobileCommentsController::class, 'index'])->name('comments.index');
            Route::post('comments', [MobileCommentsController::class, 'store'])->middleware(\App\Http\Middleware\FilterProfanity::class)->name('comments.store');
        });

        // Certificate
        Route::get('courses/{course}/certificate', [MobileProgressController::class, 'getCertificate'])->name('certificate');
        Route::post('certificates/generate', [\App\Http\Controllers\Api\Mobile\MobileCertificateController::class, 'generate'])->name('certificates.generate');

        // Leaderboard
        Route::get('leaderboard', [\App\Http\Controllers\Api\Mobile\MobileLeaderboardController::class, 'index'])->name('leaderboard.index');

        // Quiz
        Route::prefix('quizzes/{quiz}')->name('quizzes.')->group(function () {
            Route::get('/',      [MobileQuizController::class, 'show'])->name('show');
            Route::post('submit',[MobileQuizController::class, 'submit'])->name('submit');
        });

        // Wishlist
        Route::prefix('wishlist')->name('wishlist.')->group(function () {
            Route::get('/',               [MobileWishlistController::class, 'index'])->name('index');
            Route::post('{course}/toggle',[MobileWishlistController::class, 'toggle'])->name('toggle');
        });

        // Ratings
        Route::post('courses/{course}/ratings', [MobileCourseRatingController::class, 'store'])->name('ratings.store');

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',            [MobileNotificationController::class, 'index'])->name('index');
            Route::get('unread-count', [MobileNotificationController::class, 'unreadCount'])->name('unread-count');
            Route::post('{id}/read',   [MobileNotificationController::class, 'markRead'])->name('mark-read');
            Route::post('read-all',    [MobileNotificationController::class, 'markAllRead'])->name('mark-all-read');
        });

        // ── Tutor routes ───────────────────────────────────────────────────

        Route::prefix('tutor')->name('tutor.')->middleware('can:access-tutor-panel')->group(function () {
            Route::get('dashboard',                    [MobileTutorDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('courses',                      [MobileTutorDashboardController::class, 'courses'])->name('courses');
            Route::get('courses/{course}/students',    [MobileTutorDashboardController::class, 'students'])->name('students');
            Route::get('earnings',                     [MobileTutorDashboardController::class, 'earnings'])->name('earnings');
        });
    });
});
