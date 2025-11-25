<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RouteController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\StationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Version 1 de l'API Train Routing & Analytics
|
*/

Route::prefix('v1')->group(function () {

    // Routes d'authentification (publiques)
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    // Routes protégées par JWT
    Route::middleware('jwt.auth')->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Routing - Calcul des trajets
        Route::post('/routes', [RouteController::class, 'calculate']);
        Route::get('/routes', [RouteController::class, 'index']);
        Route::get('/routes/{id}', [RouteController::class, 'show']);

        // Analytics - Statistiques
        Route::get('/stats/dashboard', [StatsController::class, 'dashboard']);
        Route::get('/stats/distances', [StatsController::class, 'distances']);

        // Stations (endpoints utilitaires)
        Route::get('/stations', [StationController::class, 'index']);
        Route::get('/stations/{id}', [StationController::class, 'show']);
    });
});

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
});
