<?php

use App\Http\Controllers\Dashboard\{
    AuthController,
    CategoryController,
    LevelController,
    BadgeController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DashBoard API Routes
|--------------------------------------------------------------------------
|
*/

/*****************Auth Routes ****************************/

Route::middleware('auth:sanctum')->group(function () {
    // =======================  مسارات التسجيل و التسجيل دخول المدير ======================
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// =============================== مسارات التصنيف ====================================
Route::prefix('categories')->group(function () {
    // مسار العرض
    Route::get('/',               [CategoryController::class, 'index']);
    // مسار انشاء عنصر جديد
    Route::post('/store',         [CategoryController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{slug}/show',    [CategoryController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update',   [CategoryController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete',   [CategoryController::class, 'delete']);
});


// مسار تسجيل الدخول
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
