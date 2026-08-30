<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCaptcha;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\CompleteProfileController;
use App\Http\Controllers\Auth\ProviderController;
use App\Http\Controllers\App\Auth\VerifyEmailController;
use App\Http\Controllers\App\Auth\NewPasswordController;
use App\Http\Controllers\App\Auth\PasswordResetLinkController;
use App\Http\Controllers\App\Auth\EmailVerificationPromptController;
use App\Http\Controllers\App\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ImpersonationController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::middleware(['guest'])->prefix('auth')->group(function () {
    Route::view('/login', 'auth.login')->name('auth.login');
    Route::get('/register', [AuthController::class, 'show'])->name('auth.register');
    Route::get('/{provider}/callback', [ProviderController::class, 'callback'])->name('auth.google.callback');
    Route::get('/{provider}/redirect', [ProviderController::class, 'redirect'])->name('auth.google.redirect');
});

Route::post('/auth/login', [AuthController::class, 'login'])->middleware(VerifyCaptcha::class)->name('auth.do-login');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware(VerifyCaptcha::class)->name('auth.do-register');

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    ->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
    ->name('password.store');

Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::get('/auth/complete-profile', [CompleteProfileController::class, 'show'])->name('auth.complete-profile');
    Route::post('/auth/complete-profile', [CompleteProfileController::class, 'store'])->name('auth.complete-profile.store');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::get('/tutor/impersonate/start/{user}', [ImpersonationController::class, 'start'])->name('impersonation.start')->middleware(['web', 'auth']);
Route::get('/tutor/impersonate/leave', [ImpersonationController::class, 'leave'])->name('impersonation.leave')->middleware(['web']);
