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

// =============================== مسارات المستوى ==================================

Route::prefix('levels')->group(function () {
    // مسار العرض
    Route::get('/',             [LevelController::class, 'index']);
    // مسار انشاء عنصر جديد
    Route::post('/store',       [LevelController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{slug}/show',  [LevelController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update', [LevelController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete', [LevelController::class, 'delete']);
});

// =============================== مسارات الشارة ====================================

Route::prefix('badges')->group(function () {
    // مسار العرض
    Route::get('/',             [BadgeController::class, 'index']);
    // مسار انشاء عنصر جديد
    Route::post('/store',       [BadgeController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{slug}/show',  [BadgeController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update', [BadgeController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete', [BadgeController::class, 'delete']);
});
