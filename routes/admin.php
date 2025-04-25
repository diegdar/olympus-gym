<?php 

declare(strict_types=1);

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->resource('users', UserController::class)     
    ->only(['index', 'edit', 'update', 'destroy'])
    ->names('admin.users');

Route::middleware(['auth'])->resource('roles', RoleController::class)
    ->except('show')
    ->names('admin.roles');

Route::middleware(['auth'])->resource('activities', ActivityController::class)
->except('show')
->names('admin.activities');