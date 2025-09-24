<?php
declare(strict_types=1);

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityScheduleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SubscriptionController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::get('/privacy-policy', function () {
    return view('guest/privacy-policy');
})->name('privacy.policy');

Route::get('/instalaciones', function(){
    return view('guest/facilities');
})->name('facilities');

Route::get('/contact', function(){
    return view('guest/contact');
})->name('contact');

Route::get('/servicios', function(){
    return view('guest/services');
})->name('services');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth','verified','can:member.panel'])
    ->name('dashboard');

Route::middleware(['auth','verified','can:member.panel'])->group(function() {
    Route::get('member/stats/weekly-attendance', [DashboardController::class, 'weeklyAttendance'])
        ->name('member.stats.weekly-attendance');
    Route::get('member/stats/activity-distribution', [DashboardController::class, 'activityDistribution'])
        ->name('member.stats.activity-distribution');
});

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
    Route::prefix('activity-schedules')->group(function () {        
        Route::get('my-reservations', [ActivityScheduleController::class, 'showUserReservations'])
            ->name('user.reservations');

        Route::get('{activitySchedule}/enrolled-users', [ActivityScheduleController::class, 'enrolledUsers'])
            ->name('activity.schedules.enrolled-users');
        Route::put('{activitySchedule}/attendance', [ActivityScheduleController::class, 'updateAttendance'])
            ->name('activity.schedules.attendance');

        Route::post('{activitySchedule}/enroll', [ActivityScheduleController::class, 'enrollUserInSchedule'])
            ->name('activity.schedules.enroll');

        Route::delete('{activitySchedule}/unenroll', [ActivityScheduleController::class, 'unenrollUserInSchedule'])
            ->name('activity.schedules.unenroll');
    });
    Route::resource('activity-schedules', ActivityScheduleController::class)
    ->names('activity.schedules');   
    
    // Subscriptions
    Route::get('member/subscription', [SubscriptionController::class, 'index'])
        ->name('member.subscription');
    Route::put('member/subscription', [SubscriptionController::class, 'changeSubscription'])
        ->name('member.subscription.update');

});

require __DIR__.'/auth.php';
