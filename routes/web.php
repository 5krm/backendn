<?php

use App\Http\Controllers\Api\Tutors\TutorAccountController;
use App\Http\Controllers\App\Courses\CourseController;
use App\Http\Controllers\App\Courses\EnrollmentController;
use App\Http\Controllers\App\Courses\ExamController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\Emails\EmailPreferenceController;
use App\Http\Controllers\App\Lessons\LessonController;
use App\Http\Controllers\App\Lessons\LessonResourceDownloadController;
use App\Http\Controllers\App\OrganizationController;
use App\Http\Controllers\App\Profile\BillingController;
use App\Http\Controllers\App\Profile\ProfileController;
use App\Http\Controllers\App\Profile\SettingController;
use App\Http\Controllers\App\PromotionController;
use App\Http\Controllers\App\SurveyController;
use App\Http\Controllers\App\TutorController;
use App\Http\Controllers\App\YoutubeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\SitemapController;
use App\Mail\CourseRatingMail;
use App\Mail\PromotionAnnouncement;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [CourseController::class, 'index'])->name('courses');
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.details');
Route::get('/promotions/{promotion}', [PromotionController::class, 'show'])->name('promotions.show');
Route::get('/organizations/{organization:slug}', [OrganizationController::class, 'index'])
    ->name('organization.index');
Route::get('/tutor/{tutor:id}', [TutorController::class, 'index'])
    ->name('tutor.index');

Route::middleware(['auth'])->prefix('youtube')->group(function () {
    Route::get('/', [YoutubeController::class, 'index'])->name('youtube.index');
    Route::get('/callback', [YoutubeController::class, 'callback'])->name('youtube.callback');
});

Route::middleware(['auth', 'complete_profile'])->prefix('app')->group(function () {
    Route::get('/lesson-resources/{resource}/download', LessonResourceDownloadController::class)
        ->name('app.lesson.resources.download');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('app.profile');
    Route::put('/profile', [ProfileController::class, 'updateInfo'])->name('app.profile.update');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('app.profile.change-password');
    Route::put('/change-avatar', [ProfileController::class, 'changeAvatar'])->name('app.profile.change-avatar');

    Route::get('/settings', [SettingController::class, 'index'])->name('app.settings');
    Route::put('/settings', [SettingController::class, 'updateDisplayLanguage'])
        ->name('app.settings.update-display-language');

    Route::put('/settings/update-display-language', [SettingController::class, 'updateEmailPreferences'])
        ->name('app.settings.update-email-preferences');

    Route::get('/billing', [BillingController::class, 'index'])->name('app.billing');
    Route::get('/billing/invoice/courses/{course}', [BillingController::class, 'courseInvoice'])->name('app.billing.courseInvoice');

    Route::group(['prefix' => 'courses'], function () {
        Route::get('/', [CourseController::class, 'index'])->name('app.courses');
        Route::group(['prefix' => '/lessons/{lesson}', 'middleware' => ['check_lesson']], function () {
            Route::get('/', [LessonController::class, 'show'])->name('app.lessons.lesson');
            Route::post('/complete', [LessonController::class, 'markComplete'])->name('app.lessons.complete_lesson');
        });

        Route::group(['middleware' => ['check_course'], 'prefix' => '/{course}'], function () {

            Route::get('', [CourseController::class, 'show'])->name('app.courses.details');
            Route::get('/lessons', [LessonController::class, 'index'])->name('app.lessons.by-course');
            Route::post('/enroll', [EnrollmentController::class, 'store'])->name('app.courses.enroll');
            // Route::get('/wishlist', [CourseController::class, 'toggleWishlist'])->name('app.courses.wishlist');
            Route::group(['prefix' => '/exam'], function () {
                Route::get('/info', [ExamController::class, 'info'])->name('app.courses.exam-info');
                Route::get('', [ExamController::class, 'get'])->name('app.courses.exam');
                Route::post('', [ExamController::class, 'save'])->name('app.courses.send_exam');
                Route::get('/access-denied', [ExamController::class, 'access_denied'])->name('app.courses.exam.access-denied');
            });
            Route::get('/enroll', [EnrollmentController::class, 'index'])->middleware('verified')->name('app.courses.enroll.form');
            Route::get('/enroll/process', [EnrollmentController::class, 'process'])->name('app.courses.enroll.process');
            Route::get('/enroll/success', [EnrollmentController::class, 'success'])->name('app.courses.enroll.success');
            Route::get('/get-certificate', [CourseController::class, 'fetch_certificate'])->name('app.courses.certificate');

            Route::view('/rating', 'app.courses.rating')->middleware('can_rate_course')->name('app.courses.rate');
            Route::get('/survey', [SurveyController::class, 'index'])->name('app.survey');
            Route::post('/survey', [SurveyController::class, 'store'])->name('app.survey.store');

            Route::post('/wishlist', [DashboardController::class, 'removeWishCourse'])->name('app.courses.wishlist.destroy');
        });
    });
});

Route::get('/switch-language/{lang}', [LanguageController::class, 'switch'])->name('languages.switch');
Route::prefix('/email/{token}')->group(function () {
    Route::get('/unsubscribe/{type}', [EmailPreferenceController::class, 'unsubscribe'])->name('email.unsubscribe');
    Route::post('/subscribe/{type}', [EmailPreferenceController::class, 'subscribe'])->name('email.subscribe');
});
Route::prefix('/tutors/{token}/setup')->group(function () {
    Route::get('/', [TutorAccountController::class, 'form'])->name('email.setup_tutor');
    Route::post('/', [TutorAccountController::class, 'setup'])->name('email.setup_tutor_setup');
});

Route::get('/certificate/{certificate}/download', [CertificateVerificationController::class, 'download'])->name('certificate.download');

Route::get('/course-certificate/{course}/certificate', function (Course $course) {
    $certificate = Certificate::where('course_id', $course->id)->where('user_id', auth()->id())->first();

    return redirect(route('certificates.verify', $certificate->verification_code));
})->name('certificate_by_course');

Route::get('/certificates/verify/{code}', [CertificateVerificationController::class, 'show'])
    ->name('certificates.verify');

// Legal pages
Route::get('/faq', [LegalController::class, 'faq'])->name('legal.faq');
Route::get('/privacy-policy', [LegalController::class, 'privacyPolicy'])->name('legal.privacy-policy');
Route::get('/terms-of-service', [LegalController::class, 'termsOfService'])->name('legal.terms-of-service');
Route::get('/cookie-policy', [LegalController::class, 'cookiePolicy'])->name('legal.cookie-policy');
Route::get('/contact', [LegalController::class, 'contact'])->name('legal.contact');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

require __DIR__.'/auth.php';
Route::get('/preview-mail', function () {
    $user = User::first() ?? new User(['name' => 'Sarah']);
    $course = Course::first();

    return new CourseRatingMail(
        user: $user,
        course: $course
    );
});

Route::get('/preview-promotion-mail', function () {
    $user = User::first() ?? new User(['name' => 'Sarah']);
    $course = Course::first();
    $promotion = Promotion::query()->active()->first() ?? Promotion::first();

    return new PromotionAnnouncement(
        user: $user,
        promotion: $promotion
    );
});

Route::get('/artisan/migrate/{key}', function (string $key) {
    if ($key !== config('app.key') && $key !== env('MIGRATION_KEY', 'migrate123')) {
        abort(403, 'Unauthorized');
    }

    try {
        Artisan::call('migrate', ['--force' => true]);

        return response('<pre>Migration Output:\n'.Artisan::output().'</pre>');
    } catch (Throwable $e) {
        return response('<pre>Migration Failed:\n'.$e->getMessage().'\n'.$e->getTraceAsString().'</pre>', 500);
    }
});
