<?php

use App\Http\Controllers\Dashboard\{
    ActivedProductController,
    AuthController,
    CategoryController,
    CountryController,
    SubCategoryController,
    LevelController,
    BadgeController,
    ProductController,
    TagController
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
    Route::get('/{id}',           [CategoryController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update',   [CategoryController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete',   [CategoryController::class, 'delete']);
});

// =============================== مسارات التصنيف الفرعي ====================================
Route::prefix('subcategories')->group(function () {

    // مسار عرض عنصر من اجل انشاء
    Route::get('/create',           [SubCategoryController::class, 'create']);
    // مسار انشاء عنصر جديد
    Route::post('/store',           [SubCategoryController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{id}',             [SubCategoryController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update',     [SubCategoryController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete',     [SubCategoryController::class, 'delete']);
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
    Route::get('/{id}',         [LevelController::class, 'show']);
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
    Route::get('/{id}',         [BadgeController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update', [BadgeController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete', [BadgeController::class, 'delete']);
});

// =============================== مسارات الخدمة ====================================
Route::prefix('products')->group(function () {
    // مسار العرض الخدمات
    Route::get('/',                     [ProductController::class, 'index']);
    // مسار العرض الخدمات التي تم تنشيطها 
    Route::get('/active/status',        [ProductController::class, 'getRroductsActived']);
    // مسار العرض الخدمات التي تم تنشيطها 
    Route::get('/reject/status',        [ProductController::class, 'getProductsRejected']);
    // مسار جلب عنصر الواحد
    Route::get('/{id}',                 [ProductController::class, 'show']);
    // مسار تنشيط الخدمة
    Route::post('/{id}/activeProduct',  [ActivedProductController::class, 'activeProduct']);
    // مسار رفض الخدمة
    Route::post('/{id}/rejectProduct',  [ActivedProductController::class, 'rejectProduct']);
});

// =============================== مسارات الوسم ====================================
Route::prefix('tags')->group(function () {
    // مسار العرض
    Route::get('/',               [TagController::class, 'index']);
    // مسار انشاء عنصر جديد
    Route::post('/store',         [TagController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{id}',           [TagController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update',   [TagController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete',   [TagController::class, 'delete']);
});
// =============================== مسارات الدولة ==================================

Route::prefix('countries')->group(function () {
    // مسار العرض
    Route::get('/',             [CountryController::class, 'index']);
    // مسار انشاء عنصر جديد
    Route::post('/store',       [CountryController::class, 'store']);
    // مسار جلب عنصر الواحد
    Route::get('/{id}',         [CountryController::class, 'show']);
    // مسار التعديل على العنصر
    Route::post('/{id}/update', [CountryController::class, 'update']);
    // مسار حذف العنصر
    Route::post('/{id}/delete', [CountryController::class, 'delete']);
});
