<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordChangeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LetterDispositionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

    // Di luar middleware password.change supaya tidak infinite redirect.
    Route::get('password/change', [PasswordChangeController::class, 'edit'])->name('password.change');
    Route::put('password/change', [PasswordChangeController::class, 'update'])->name('password.update');

    Route::middleware('password.change')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('letters', [LetterController::class, 'index'])->name('letters.index');
        Route::get('letters/create', [LetterController::class, 'create'])->name('letters.create')->middleware('can:letters.create');
        Route::post('letters', [LetterController::class, 'store'])->name('letters.store')->middleware('can:letters.create');
        Route::get('letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
        Route::get('letters/{letter}/edit', [LetterController::class, 'edit'])->name('letters.edit')->middleware('can:letters.update');
        Route::put('letters/{letter}', [LetterController::class, 'update'])->name('letters.update')->middleware('can:letters.update');
        Route::delete('letters/{letter}', [LetterController::class, 'destroy'])->name('letters.destroy')->middleware('can:letters.update');

        Route::post('letters/{letter}/dispositions', [LetterDispositionController::class, 'store'])->name('letters.dispositions.store')->middleware('can:letters.dispose');
        Route::delete('letters/{letter}/dispositions/{disposition}', [LetterDispositionController::class, 'destroy'])->name('letters.dispositions.destroy')->middleware('can:letters.dispose');

        Route::prefix('admin')->name('admin.')->middleware('can:admin.users')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

            Route::middleware('can:admin.roles')->group(function () {
                Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
                Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
            });
        });
    });
});
