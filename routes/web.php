<?php

use App\Http\Controllers\BodyStatController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProgressController;
use App\Http\Controllers\SupervisorController;
use Illuminate\Support\Facades\Route;

// 顧客専用公開URL（認証不要）
Route::get('/progress/{uuid}', [PublicProgressController::class, 'show'])
    ->name('public.progress');

// 認証必須ルート
Route::middleware(['auth', 'verified'])->group(function () {

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // 顧客管理（トレーナー・責任者共通）
    Route::resource('clients', ClientController::class);

    // 体の計測ログ管理
    Route::post('/clients/{client}/body-stats', [BodyStatController::class, 'store'])
        ->name('body-stats.store');
    Route::delete('/clients/{client}/body-stats/{bodyStat}', [BodyStatController::class, 'destroy'])
        ->name('body-stats.destroy');

    // プロフィール管理
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // 責任者専用
    Route::middleware('role:supervisor')->prefix('supervisor')->group(function () {
        Route::get('/', [SupervisorController::class, 'index'])
            ->name('supervisor.index');
    });
});

require __DIR__.'/auth.php';