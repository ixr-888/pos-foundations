<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public pages: no login or email verification is required to read tasks.
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/profile', [PageController::class, 'profile'])->name('profile');
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');

// Declare /tasks/create before /tasks/{task}.
Route::middleware('auth')->group(function (): void {
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::redirect('/dashboard', '/tasks')->name('dashboard');
    Route::resource('tasks', TaskController::class)->only([
        'create', 'store', 'edit', 'update', 'destroy',
    ]);
    // Editable account settings are separate from the public project profile.
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});

Route::get('/tasks/{task}', [TaskController::class, 'show'])
    ->whereNumber('task')->name('tasks.show');

require __DIR__.'/auth.php';
