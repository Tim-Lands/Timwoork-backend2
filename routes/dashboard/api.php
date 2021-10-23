<?php

use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\BadgeController;
use App\Http\Controllers\Dashboard\LevelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DashBoard API Routes
|--------------------------------------------------------------------------
|
*/

/*****************Auth Routes ****************************/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/login', [AuthController::class, 'login']);

/****************** End Auth Routes ***********************/

Route::prefix('levels')->group(function () {
    Route::get('/', [LevelController::class, 'index']);
    Route::post('/store', [LevelController::class, 'store']);
    Route::get('/{id}', [LevelController::class, 'show']);
    Route::post('/{id}/update', [LevelController::class, 'update']);
    Route::post('/{id}/delete', [LevelController::class, 'destroy']);
});

Route::prefix('badges')->group(function () {
    Route::get('/', [BadgeController::class, 'index']);
    Route::post('/store', [BadgeController::class, 'store']);
    Route::get('/{id}', [BadgeController::class, 'show']);
    Route::post('/{id}/update', [BadgeController::class, 'update']);
    Route::post('/{id}/delete', [BadgeController::class, 'destroy']);
});
