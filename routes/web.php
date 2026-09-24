<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AgendaBookController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\LetterDispositionController;
use App\Http\Controllers\LetterDivisionController;
use App\Http\Controllers\LetterDivisionDispositionController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/**
 * Prefix /surat-new -- aplikasi ini dimaksudkan berbagi domain dengan
 * aplikasi PTPN lain (portal-new, dsb) di path terpisah, bukan root.
 * Nama route TIDAK berubah (login, dashboard, letters.index, dst),
 * hanya URL-nya -- semua link di aplikasi pakai route()/redirect()->route()
 * jadi otomatis ikut prefix ini tanpa perlu diubah satu per satu.
 */
Route::redirect('/', '/surat-new');
Route::redirect('surat', '/surat-new');

Route::prefix('surat-new')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));

    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('letters', [LetterController::class, 'index'])->name('letters.index');
        Route::get('letters/export', [LetterController::class, 'export'])->name('letters.export');
        Route::post('letters/data', [LetterController::class, 'data'])->name('letters.data');
        Route::get('letters/create', [LetterController::class, 'create'])->name('letters.create')->middleware('can:letters.create');
        Route::post('letters', [LetterController::class, 'store'])->name('letters.store')->middleware('can:letters.create');
        Route::get('letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
        Route::get('letters/{letter}/print', [LetterController::class, 'print'])->name('letters.print');
        Route::get('letters/{letter}/edit', [LetterController::class, 'edit'])->name('letters.edit')->middleware('can:letters.update');
        Route::put('letters/{letter}', [LetterController::class, 'update'])->name('letters.update')->middleware('can:letters.update');
        Route::delete('letters/{letter}', [LetterController::class, 'destroy'])->name('letters.destroy')->middleware('can:letters.update');

        Route::post('letters/{letter}/dispositions', [LetterDispositionController::class, 'store'])->name('letters.dispositions.store')->middleware('can:letters.dispose');
        Route::delete('letters/{letter}/dispositions/{disposition}', [LetterDispositionController::class, 'destroy'])->name('letters.dispositions.destroy')->middleware('can:letters.dispose');

        Route::get('agenda-book', [AgendaBookController::class, 'index'])->name('agenda-book.index');

        Route::get('reports/follow-up', [ReportController::class, 'followUp'])->name('reports.follow-up');
        Route::post('reports/follow-up/data', [ReportController::class, 'followUpData'])->name('reports.follow-up.data');

        Route::prefix('letter-divisions')->name('letter-divisions.')->middleware('can:letter-divisions.manage')->group(function () {
            Route::get('/', [LetterDivisionController::class, 'index'])->name('index');
            Route::post('data', [LetterDivisionController::class, 'data'])->name('data');
            Route::get('create', [LetterDivisionController::class, 'create'])->name('create');
            Route::post('/', [LetterDivisionController::class, 'store'])->name('store');
            Route::get('{division}', [LetterDivisionController::class, 'show'])->name('show');
            Route::get('{division}/edit', [LetterDivisionController::class, 'edit'])->name('edit');
            Route::put('{division}', [LetterDivisionController::class, 'update'])->name('update');
            Route::delete('{division}', [LetterDivisionController::class, 'destroy'])->name('destroy');

            Route::post('{division}/dispositions', [LetterDivisionDispositionController::class, 'store'])->name('dispositions.store');
        });

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
