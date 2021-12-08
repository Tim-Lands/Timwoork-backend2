<?php

use App\Http\Controllers\Auth\{LoginController, RegisterController};
use App\Http\Controllers\{
    ChatController,
    ConversationController,
    FilterController,
    FrontEndController,
    Product\InsertProductContoller,
    Product\DeleteProductController,
    Product\ShortenerController,
    Product\SellerController,
    ProfileController,
    SalesProcces\CartController,
    SalesProcces\OrderController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->json('هذا الرابط غير موجود ', 200);
});
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
    // انشاء الخدمة
    Route::get('/store',                     [InsertProductContoller::class, 'store']);
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


// ===================== المحادثات والرسائل ============================================

Route::prefix('conversations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::get('/{id}', [ConversationController::class, 'show']);
    Route::post('/store', [ConversationController::class, 'store']);
    Route::post('/{conversation}/sendMessage', [ConversationController::class, 'sendMessage']);
});
// =============================== مسارات انشاء عناصر جديدة فالسلة ==================================
Route::prefix('cart')->group(function () {
    // عرض السلة
    Route::get('/',                             [CartController::class, 'index']);
    // انشاء عنصر فالسلة
    Route::post('/store',                       [CartController::class, 'store']);
    // تحديث عنصر فالسلة
    Route::post('/cartitem/update/{id}',         [CartController::class, 'update']);
    //حذف عنصر من السلة
    Route::post('/cartitem/delete/{id}',                 [CartController::class, 'delete']);

});

// =============================== مسارات انشاء عناصر جديدة فالسلة ==================================
Route::prefix('order')->group(function () {
    // عرض السلة
    Route::get('/',               [OrderController::class, 'index']);
    // انشاء عنصر فالسلة
    Route::post('/store',         [OrderController::class, 'createOrderWithItems']);
    //حذف عنصر من السلة
    //Route::post('/{id}/delete',    [OrderController::class, 'delete']);
});


// عرض التصنيفات الرئيسية و الفرعية
Route::get('/display_categories', [FrontEndController::class, 'get_categories_subcategories_porducts']);
// عرض التصنيفات الرئيسية
Route::get('/get_categories', [FrontEndController::class, 'get_categories']);
// عرض التصنيفات الفرعية
Route::get('/get_categories/{id}', [FrontEndController::class, 'get_subcategories']);
// عرض الخدمة الواحدة
Route::get('product/{slug}',                    [FrontEndController::class, 'show']);
// عرض جميع الخدمات 
Route::get('/get_products', [FrontEndController::class, 'getProducts']);

Route::prefix('filter')->group(function () {

    Route::get('/', FilterController::class);
});

