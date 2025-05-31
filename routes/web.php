<?php
declare(strict_types=1);

use App\Http\Controllers\ActivityScheduleController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy.policy');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Activities
    Route::resource('activities/schedule', ActivityScheduleController::class)
    ->names('activities.schedule');    
});

require __DIR__.'/auth.php';
