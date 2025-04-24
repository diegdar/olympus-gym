<?php 

declare(strict_types=1);

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class)     
    ->only(['index', 'edit', 'update', 'destroy'])
    ->names('admin.users');

Route::resource('roles', RoleController::class)
    ->except('show')
    ->names('admin.roles');