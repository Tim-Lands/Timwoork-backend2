<?php

use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\{
    Product\InsertProductContoller,
    Product\DeleteProductController,
    Product\SellerController,
    ProfileController
};
use App\Http\Controllers\Product\ShortenerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/*****************Auth Routes ****************************/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);
});

Route::post('/login', [LoginController::class, 'login']);

Route::get('/login/{provider}', [LoginController::class, 'redirectToProvider']);
Route::get('/login/{provider}/callback', [LoginController::class, 'handleProviderCallback']);

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/email/verify', [RegisterController::class, 'verifyEmail']);
Route::post('/email/resend', [RegisterController::class, 'resend_verify_code']);

/****************** End Auth Routes ***********************/

// =============================== مسارات الملف الشخصي ==================================

Route::prefix('profiles')->group(function () {

    Route::middleware('auth:sanctum')->post('/step_one', [ProfileController::class, 'step_one']);
    Route::middleware('auth:sanctum')->post('/step_two', [ProfileController::class, 'step_two']);
    Route::middleware('auth:sanctum')->post('/step_three', [ProfileController::class, 'step_three']);
    Route::get('/{username}', [ProfileController::class, 'show']);
});


// ===============================   مسارات الملف الشخصي البائع==================================

Route::prefix('sellers')->group(function () {
    Route::middleware('auth:sanctum')->post('/store', [SellerController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/step_one', [SellerController::class, 'step_one']);
    Route::middleware('auth:sanctum')->post('/step_two', [SellerController::class, 'step_two']);
});


// =============================== مسارات انشاء خدمة جديدة ==================================
Route::prefix('product')->group(function () {
    // عرض الخدمة
    Route::get('/{slug}',                    [InsertProductContoller::class, 'show']);
    // انشاء خدمة جديدة
    Route::get('create',                     [InsertProductContoller::class, 'create']);
    // المحلة الاولى
    Route::post('{id}/product-step-one',     [InsertProductContoller::class, 'storeStepOne']);
    // المحلة الثانية
    Route::post('/{id}/product-step-two',    [InsertProductContoller::class, 'storeStepTwo']);
    // المحلة الثالثة
    Route::post('/{id}/product-step-three',  [InsertProductContoller::class, 'storeStepThree']);
    // المحلة الرابعة
    Route::post('/{id}/product-step-four',   [InsertProductContoller::class, 'storeStepFour']);
    // المحلة الخامسة
    Route::post('/{id}/product-step-five',   [InsertProductContoller::class, 'storeStepFive']);
    // حذف الخدمة
    Route::post('/{id}/deleteProduct',       DeleteProductController::class);
});
// ======================== مسار رابط المختصر للخدمة ==================================
Route::get('/s/{code}', ShortenerController::class);
