<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CanRateCourse;
use App\Http\Middleware\CheckApiVersion;
use App\Http\Middleware\CheckCourseAccess;
use App\Http\Middleware\CheckLessonAccess;
use App\Http\Middleware\EnsureCompleteProfile;
use App\Http\Middleware\EnsureIsTutor;
use App\Http\Middleware\Language;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SetApiLocale;
use App\Http\Middleware\VerifyAppKey;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api/tutor/v1')
                ->group(base_path('routes/tutor.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*'); // Trust all proxies
        $middleware->preventRequestForgery(except: [
            'telescope/*',
        ]);

        $middleware->web(append: [
            Language::class,
        ]);

        $middleware->alias([
            'app_key' => VerifyAppKey::class,
            'auth' => Authenticate::class,
            'guest' => RedirectIfAuthenticated::class,
            'check_course' => CheckCourseAccess::class,
            'can_rate_course' => CanRateCourse::class,
            'check_lesson' => CheckLessonAccess::class,
            'complete_profile' => EnsureCompleteProfile::class,
            'can:access-tutor-panel' => EnsureIsTutor::class,
            'check_api_version' => CheckApiVersion::class,
            'api_locale' => SetApiLocale::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('auth:clear-resets')->daily();
        $schedule->command('app:send-followup-emails')->daily();
        $schedule->command('app:send-exam-reminder-email')->daily();
        $schedule->command('app:send-course-rating-reminder')->daily();
        $schedule->command('app:send-course-suggestion')->daily();
        $schedule->command('app:mail-course-inactive-students')->daily();

        if (App::isProduction()) {
            $schedule->command('app:backup:generate')->hourly();
            $schedule->command('app:backup:cleanup')->daily();
        }

        // reset youtube upload quota at midnight Pacific Time.
        $schedule
            ->call(function () {
                // initialize on first run if not already set
                if (! Cache::has('youtube_upload_quota')) {
                    Cache::put('youtube_upload_quota', 6, 24 * 60 * 60);
                }

                Cache::put('youtube_upload_quota', 6, 24 * 60 * 60);
            })
            ->daily()
            ->timezone('America/Los_Angeles');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/mobile/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/mobile/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], $e->status);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/mobile/v1/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                ], 404);
            }
        });
    })
    ->create();
