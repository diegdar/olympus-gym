<?php 

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HomeController;

Route::resource('users', UserController::class)
    ->only(['index', 'edit', 'update', 'destroy'])
    ->names('admin.users');