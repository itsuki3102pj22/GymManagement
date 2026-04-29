<?php

namespace App\Providers;

use App\Services\BodyStatsService;
use App\Services\NutritionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(NutritionService::class);
        $this->app->singleton(BodyStatsService::class);
        $this->app->singleton(LineFoodLogService::class);
        $this->app->singleton(LineReservationService::class);
        $this->app->singleton(LineReplyService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
