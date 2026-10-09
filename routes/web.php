<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\ShortLinkController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('health');

Route::get('/', fn () => auth()->check() ? redirect()->route('links.index') : view('landing'))->name('home');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::resource('links', ShortLinkController::class);
    Route::patch('/links/{link}/toggle', [ShortLinkController::class, 'toggle'])->name('links.toggle');
    Route::get('/links/{link}/analytics', [AnalyticsController::class, 'show'])->name('links.analytics');
    Route::get('/links/{link}/qr.svg', [QrCodeController::class, 'show'])->name('links.qr');

    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.users.index'));
        Route::resource('users', UserController::class)->except(['show', 'destroy']);
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('/messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::patch('/messages/{message}/unread', [ContactMessageController::class, 'markUnread'])->name('messages.unread');
        Route::delete('/messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
    });
});

// Redirect publik harus menjadi rute terakhir agar tidak mengambil alih rute aplikasi.
Route::get('/{alias}', RedirectController::class)
    ->where('alias', '[A-Za-z0-9][A-Za-z0-9_-]{0,63}')
    ->middleware('throttle:redirect')
    ->name('redirect');
