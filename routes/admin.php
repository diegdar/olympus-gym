<?php 

declare(strict_types=1);

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubscriptionStatsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->resource('users', UserController::class)     
    ->only(['index', 'edit', 'update', 'destroy'])
    ->names('admin.users');

Route::middleware(['auth'])->resource('roles', RoleController::class)
    ->except('show')
    ->names('admin.roles');

// Subscription statistics
Route::middleware(['auth','can:admin.subscriptions.stats'])->group(function () {
    Route::get('subscriptions/stats', [SubscriptionStatsController::class, 'index'])
        ->name('admin.subscriptions.stats');
    Route::get('subscriptions/stats/percentages', [SubscriptionStatsController::class, 'percentages'])
        ->name('admin.subscriptions.percentages');
});