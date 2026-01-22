<?php

use App\Http\Controllers\Api\V1\AttractionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\ParkController;
use App\Http\Controllers\Api\V1\StateController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| AquaGuia API v1 - Guia de Parques Aquáticos
|
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    // Authentication (Public)
    // ==========================================
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Authentication (Protected)
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    // ==========================================
    // States & Cities
    // ==========================================
    Route::get('/states', [StateController::class, 'index']);
    Route::get('/states/{abbr}/cities', [StateController::class, 'cities']);
    Route::get('/cities', [CityController::class, 'index']);

    // ==========================================
    // Tags
    // ==========================================
    Route::get('/tags', [TagController::class, 'index']);

    // ==========================================
    // Parks
    // ==========================================
    Route::get('/parks', [ParkController::class, 'index']);
    Route::get('/parks/{park}', [ParkController::class, 'show']);
    Route::get('/parks/{park}/attractions', [AttractionController::class, 'index']);

    // ==========================================
    // Favorites (Protected)
    // ==========================================
    Route::middleware('auth:api')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{park}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{park}', [FavoriteController::class, 'destroy']);
    });
});
