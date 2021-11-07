<?php

use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\{
    Product\InsertProductContoller,
    ProfileController,
    SellerController
};
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
Route::prefix('addedProduct')->group(function () {
    Route::get('create',                     [InsertProductContoller::class, 'create']);
    Route::post('/product-step-one',         [InsertProductContoller::class, 'storeStepOne']);
    Route::post('/{id}/product-step-two',    [InsertProductContoller::class, 'storeStepTwo']);
    Route::post('/{id}/product-step-three',  [InsertProductContoller::class, 'storeStepThree']);
    Route::post('/{id}/product-step-four',   [InsertProductContoller::class, 'storeStepFour']);
    Route::post('/{id}/product-step-five',   [InsertProductContoller::class, 'storeStepFive']);
});
