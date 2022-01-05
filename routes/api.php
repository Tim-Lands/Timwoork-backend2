<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\DarkModeController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MyProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserStatusController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\Product\InsertProductContoller;
use App\Http\Controllers\Product\DeleteProductController;
use App\Http\Controllers\Product\ShortenerController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesProcces\CartController;
use App\Http\Controllers\SalesProcces\OrderController;
use App\Http\Controllers\SalesProcces\ItemController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Product\RatingController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::fallback(function () {
    return response()->json('هذا الرابط غير موجود ', 200);
});
Route::post('/login', [LoginController::class, 'login']);
/* -------------------------------------------------------------------------- */
/*                                 Auth Routes                                */
/* -------------------------------------------------------------------------- */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [LoginController::class, 'me']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/{user}/online', [UserStatusController::class, 'online']);
    Route::post('/{user}/offline', [UserStatusController::class, 'offline']);
    Route::post('/mode', DarkModeController::class);
    Route::post('/password/change', ChangePasswordController::class);
});

Route::prefix('my_products')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [MyProductController::class, 'index']);
    Route::get('/published', [MyProductController::class, 'published']);
    Route::get('/paused', [MyProductController::class, 'paused']);
    Route::get('/pending', [MyProductController::class, 'pending']);
    Route::get('/rejected', [MyProductController::class, 'rejected']);
    Route::get('/drafts', [MyProductController::class, 'drafts']);
    // تنشيط الخدمة
    Route::post('{id}/active_product', [MyProductController::class, 'active_product_by_seller']);
    // تعطيل الخدمة
    Route::post('{id}/disactive_product', [MyProductController::class, 'disactive_product_by_seller']);
});
Route::post('/password/forget/sendResetLink', [ForgetPasswordController::class, 'send_token']);
Route::post('/password/forget/verify', [ForgetPasswordController::class, 'verify_token']);
Route::post('/password/forget/reset', [ForgetPasswordController::class, 'reset_password']);



Route::post('/login/{provider}', [LoginController::class, 'handleProviderCallback']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/email/verify', [RegisterController::class, 'verifyEmail']);
Route::post('/email/resend', [RegisterController::class, 'resend_verify_code']);
/* -------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------- */
/*                             مسارات الملف الشخصي                            */
/* -------------------------------------------------------------------------- */

Route::prefix('profiles')->group(function () {
    Route::middleware('auth:sanctum')->post('/step_one', [ProfileController::class, 'step_one']);
    Route::middleware('auth:sanctum')->post('/step_two', [ProfileController::class, 'step_two']);
    Route::middleware('auth:sanctum')->post('/step_three', [ProfileController::class, 'step_three']);
    Route::get('/{username}', [ProfileController::class, 'show']);
});


/* -------------------------------------------------------------------------- */
/*                         مسارات الملف الشخصي البائع                         */
/* -------------------------------------------------------------------------- */

Route::prefix('sellers')->group(function () {
    Route::middleware('auth:sanctum')->post('/store', [SellerController::class, 'store']);
    Route::middleware('auth:sanctum')->post('/detailsStore', [SellerController::class, 'detailsStore']);
    Route::middleware('auth:sanctum')->post('/step_one', [SellerController::class, 'step_one']);
    Route::middleware('auth:sanctum')->post('/step_two', [SellerController::class, 'step_two']);
});


/* -------------------------------------------------------------------------- */
/*                           مسارات انشاء خدمة جديدة                          */
/* -------------------------------------------------------------------------- */
Route::prefix('product')->middleware('auth:sanctum')->group(function () {
    // انشاء الخدمة
    Route::get('/store', [InsertProductContoller::class, 'store']);
    // المحلة الاولى
    Route::post('{id}/product-step-one', [InsertProductContoller::class, 'storeStepOne']);
    // المحلة الثانية
    Route::post('/{id}/product-step-two', [InsertProductContoller::class, 'storeStepTwo']);
    // المحلة الثالثة
    Route::post('/{id}/product-step-three', [InsertProductContoller::class, 'storeStepThree']);
    // المحلة الرابعة
    Route::post('/{id}/product-step-four', [InsertProductContoller::class, 'storeStepFour']);
    // المحلة الخامسة
    Route::post('/{id}/product-step-five', [InsertProductContoller::class, 'storeStepFive']);
    // حذف الخدمة
    Route::post('/{id}/deleteProduct', DeleteProductController::class);

    // تقييم الخدمة

    Route::post('/{id}/rating', [RatingController::class, 'rate']);
});

/* -------------------------------------------------------------------------- */
/*                          مسار رابط المختصر للخدمة                          */
/* -------------------------------------------------------------------------- */
Route::get('/s/{code}', ShortenerController::class);


/* -------------------------------------------------------------------------- */
/*                             المحادثات والرسائل                             */
/* -------------------------------------------------------------------------- */

Route::prefix('conversations')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ConversationController::class, 'index']);
    Route::get('/{id}', [ConversationController::class, 'show']);
    Route::post('/store', [ConversationController::class, 'store']);
    Route::post('/{conversation}/sendMessage', [ConversationController::class, 'sendMessage']);
});

/* -------------------------------------------------------------------------- */
/*                       مسارات انشاء عناصر جديدة فالسلة                      */
/* -------------------------------------------------------------------------- */

Route::prefix('cart')->group(function () {
    // عرض السلة
    Route::get('/', [CartController::class, 'index']);
    // انشاء عنصر فالسلة
    Route::post('/store', [CartController::class, 'store']);
    // تحديث عنصر فالسلة
    Route::post('/cartitem/update/{id}', [CartController::class, 'update']);
    //حذف عنصر من السلة
    Route::post('/cartitem/delete/{id}', [CartController::class, 'delete']);
});

/* -------------------------------------------------------------------------- */
/*                            مسارات انشاء الطلبية                            */
/* -------------------------------------------------------------------------- */

Route::prefix('order')->group(function () {
    // انشاء الطلبية و ارسال الطلبيات للبائعين
    Route::post('/store', [OrderController::class, 'create_order_with_items']);
    /* ------------------ مسارات المعاملة بين البائع و المشتري ------------------ */
    Route::prefix('items')->group(function () {
        // قبول الطلبية من قبل البائع
        Route::post('/{id}/accept_item_seller', [ItemController::class, 'item_accepted_by_seller']);
        // رفض الطلبية من قبل البائع
        Route::post('/{id}/reject_item_anyone', [ItemController::class, 'item_rejected_anyone']);
        // رفض المشروع
        Route::post('/{id}/upload_resources', [ItemController::class, 'upload_resource_by_seller']);
        // تسليم المشروع من قبل البائع
        Route::post('/{id}/dilevery_resources', [ItemController::class, 'delivery_resource_by_seller']);
        // استلام المشروع من قبل المشتري و قبوله
        Route::post('/{id}/accepted_dilevery_resources', [ItemController::class, 'accepted_delivery_resource_by_buyer']);
        // رفض المشروع من قبل المشتري
        Route::post('/{id}/rejected_dilevery_resources', [ItemController::class, 'rejected_delivery_resource_by_buyer']);
        // عرض الالغاء طلب الخدمة
        Route::get('/{id}/display_item_rejected', [ItemController::class, 'display_item_rejected']);
        // طلب الالغاء الخدمة من قبل البائع
        Route::post('/{id}/request_cancel_item_by_seller', [ItemController::class, 'request_cancel_item_by_seller']);
        // طلب الالغاء الخدمة من قبل المشتري
        Route::post('/{id}/request_cancel_item_by_buyer', [ItemController::class, 'request_cancel_item_by_buyer']);
        //  قبول طلب الالغاء الخدمة من قبل البائع
        Route::post('/{id}/accept_cancel_request_by_seller', [ItemController::class, 'accept_cancel_request_by_seller']);
        //  قبول طلب الالغاء الخدمة من قبل المشتري
        Route::post('/{id}/accept_cancel_request_by_buyer', [ItemController::class, 'accept_cancel_request_by_buyer']);
        //  رفض طلب الالغاء الخدمة من قبل البائع
        Route::post('/{id}/reject_cancel_request_by_seller', [ItemController::class, 'reject_cancel_request_by_seller']);
        //  رفض طلب الالغاء الخدمة من قبل المشتري
        Route::post('/{id}/reject_cancel_request_by_buyer', [ItemController::class, 'reject_cancel_request_by_buyer']);
    });
});

/* -------------------------------------------------------------------------- */
/*                            مسارات واجهة المستخدم                           */
/* -------------------------------------------------------------------------- */

// عرض التصنيفات الرئيسية
Route::get('/get_categories', [FrontEndController::class, 'get_categories']);
// عرض التصنيفات الفرعية
Route::get('/get_categories/{id}', [FrontEndController::class, 'get_subcategories']);
// عرض الخدمة الواحدة
Route::get('product/{slug}', [FrontEndController::class, 'show']);

// مسار عملية الفلترة
Route::prefix('filter')->group(function () {
    Route::get('/', FilterController::class);
});

// مسار عملية البحث السريع

Route::prefix('search')->group(function () {
    Route::get('/', SearchController::class);
});

// مسارات تقييم الخدمة بعد نجاح عملية البيع

Route::prefix('rating')->group(function () {
    Route::post('/{id}/reply', [RatingController::class, 'reply']);
});
/* -------------------------------------------------------------------------- */
