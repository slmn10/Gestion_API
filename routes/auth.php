<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/registered', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

// Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
//     ->middleware(['signed', 'throttle:6,1'])
//     ->name('verification.verify');

Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Route::post('/email/verification-notification/{id}', [EmailVerificationNotificationController::class, 'store'])
//     ->middleware(['throttle:6,1'])
//     ->name('verification.send');

    Route::get('/email/verify', function () {

            return response()->json([
                'verified' => false,
                'message' => 'Veuillez vérifier votre adresse email, Un lien de vérification a été envoyé à l\'adresse email fournie lors de l\'inscription.',
                'url_resend_email_verification' => "url_base/email/verification-notification/user_id. Ex : https://ilera-naturals.raedd-cameroun.org/api/email/verification-notification/1",
            ], 200);
    })->name('verification.notice');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
