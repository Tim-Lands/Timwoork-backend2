<?php

use App\Http\Controllers\Dashboard\AuthController;
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
