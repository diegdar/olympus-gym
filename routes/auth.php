<?php
declare(strict_types=1);

use App\Http\Controllers\Auth\GitHubAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Livewire\Auth\ConfirmPassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorChallengeController;

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');

    // Two-factor authentication challenge
    Route::get('two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
        ->name('two-factor.login');
    Route::post('two-factor-challenge', [TwoFactorChallengeController::class, 'store'])
        ->name('two-factor.challenge');
// Github Auth
    Route::prefix('auth/github')->group(function () {
        Route::get('/', [GitHubAuthController::class, 'redirectToGitHub'])->name('auth.github');
        Route::get('callback', [GitHubAuthController::class, 'handleGitHubCallback'])->name('auth.github.callback');
    });
// Google Auth
    Route::prefix('auth/google')->group(function () {
        Route::get('/', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', ConfirmPassword::class)
        ->name('password.confirm');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
