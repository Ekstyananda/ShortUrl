<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortLinkController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('links.index'))->name('home');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('links', ShortLinkController::class);
    Route::patch('/links/{link}/toggle', [ShortLinkController::class, 'toggle'])->name('links.toggle');
    Route::get('/links/{link}/analytics', [AnalyticsController::class, 'show'])->name('links.analytics');

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.users.index'));
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
    });
});

// Redirect publik harus menjadi rute terakhir agar tidak mengambil alih rute aplikasi.
Route::get('/{alias}', RedirectController::class)
    ->where('alias', '[A-Za-z0-9][A-Za-z0-9_-]{0,63}')
    ->middleware('throttle:redirect')
    ->name('redirect');
