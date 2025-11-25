<?php

namespace App\Providers;

use App\Services\DijkstraService;
use App\Services\RouteCalculatorService;
use App\Services\StationGraphService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StationGraphService::class, function ($app) {
            return new StationGraphService();
        });

        $this->app->singleton(DijkstraService::class, function ($app) {
            return new DijkstraService();
        });

        $this->app->singleton(RouteCalculatorService::class, function ($app) {
            return new RouteCalculatorService(
                $app->make(StationGraphService::class),
                $app->make(DijkstraService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
