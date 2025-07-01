<?php
declare(strict_types=1);

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityScheduleController;
use App\Http\Controllers\RoomController;
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

    // Rooms
    Route::resource('rooms', RoomController::class)->names('rooms');

    // Activities
    Route::resource('activities', ActivityController::class)->names('activities');

    // ActivitiesSchedule
    Route::resource('activity-schedules', ActivityScheduleController::class)
    ->names('activity.schedules');
    
    Route::post('activity-schedules/{activitySchedule}/enroll', [ActivityScheduleController::class, 'enrollUserInSchedule'])->name('activity.schedules.enroll');
});

require __DIR__.'/auth.php';
