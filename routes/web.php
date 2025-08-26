<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\SocialConnectionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ProfilePhotoUrlController;

// Públicas
Route::view('/', 'welcome')->name('home');
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');


// Privadas
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

     // ===== Social (conexiones + OAuth) =====
    Route::prefix('social')->name('social.')->group(function () {

        // Pantalla para gestionar conexiones sociales
        Route::get('/connections', [SocialConnectionController::class, 'index'])
            ->name('connections');

        // Desconectar un proveedor (DELETE /social/connections/{provider})
        Route::delete('/connections/{provider}', [SocialConnectionController::class, 'destroy'])
            ->whereIn('provider', ['reddit','discord'])
            ->name('disconnect');

        // OAuth: iniciar y recibir callback
        Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
            ->whereIn('provider', ['reddit','discord'])
            ->name('redirect');

        Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
            ->whereIn('provider', ['reddit','discord'])
            ->name('callback');
    });

    // posts
Route::prefix('posts')->name('posts.')->group(function () {
Route::get('create', [PostController::class, 'create'])->name('create');
Route::post('/', [PostController::class, 'store'])->name('store');
Route::get('history', [PostController::class, 'history'])->name('history');
Route::get('{post}/schedule', [PostController::class, 'editSchedule'])->name('schedule.edit');
Route::put('{post}/schedule', [PostController::class, 'updateSchedule'])->name('schedule.update');
});

// queue
Route::prefix('queue')->name('queue.')->group(function () {
Route::get('/', [QueueController::class, 'index'])->name('index');
Route::post('{queuedPost}/cancel', [QueueController::class, 'cancel'])->name('cancel');
Route::post('{queuedPost}/send-now', [QueueController::class, 'sendNow'])->name('send_now');
});

// schedules
Route::resource('schedules', ScheduleController::class)->only(['index','store','update','destroy']);
});

Route::post('/user/profile/photo-url', [ProfilePhotoUrlController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('profile.photo-url');