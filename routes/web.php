<?php

use App\Http\Controllers\BodyStatController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProgressController;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\ReservationController;
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

    // トレーニング記録
    Route::get(
        'clients/{client}/workout-logs/create',
        [WorkoutLogController::class, 'create']
    )->name('workout-logs.create');
    Route::post(
        'clients/{client}/workout-logs',
        [WorkoutLogController::class, 'store']
    )->name('workout-logs.store');
    Route::delete(
        'clients/{client}/workout-logs/{workoutLog}',
        [WorkoutLogController::class, 'destroy']
    )->name('workout-logs.destroy');

    // 種目マスタ
    Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
    Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
    Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

    //　予約管理
    Route::resource('reservations', ReservationController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // 責任者専用
    Route::middleware('role:supervisor')->prefix('supervisor')->group(function () {
        Route::get('/', [SupervisorController::class, 'index'])
            ->name('supervisor.index');
    });
});

require __DIR__ . '/auth.php';
