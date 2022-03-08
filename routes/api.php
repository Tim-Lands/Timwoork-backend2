<?php

use App\Http\Controllers\Auth\BuyerOrderController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\DarkModeController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MyProductController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SellerOrderController;
use App\Http\Controllers\Auth\UserStatusController;
use App\Http\Controllers\Auth\WalletController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderTestController;
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
use App\Http\Controllers\WithdrawalController;
use App\Models\MoneyActivity;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Broadcast::routes(['middleware' => ['auth:sanctum']]);

// مسار الرابط
Route::fallback(function () {
    return response()->json('هذا الرابط غير موجود ', 200);
});
// مسار التسجيل الدخول
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
/* -------------------------------------------------------------------------- */
/*                             مسارات خدمات البائع                            */
/* -------------------------------------------------------------------------- */
Route::prefix('my_products')->middleware('auth:sanctum')->group(function () {
    // عرض كل الخدمات
    Route::get('/', [MyProductController::class, 'index']);
    // عرض الخدمات التي تم تنشيطها
    Route::get('/published', [MyProductController::class, 'published']);
    // عرض الخدمات المعطلة
    Route::get('/paused', [MyProductController::class, 'paused']);
    // عرض الخدمات قيد الانتظار
    Route::get('/pending', [MyProductController::class, 'pending']);
    // عرض الخدمات المرفوضة
    Route::get('/rejected', [MyProductController::class, 'rejected']);
    // عرض الخدمات الغير المكتملة
    Route::get('/drafts', [MyProductController::class, 'drafts']);
    // تنشيط الخدمة
    Route::post('{id}/active_product', [MyProductController::class, 'active_product_by_seller']);
    // تعطيل الخدمة
    Route::post('{id}/disactive_product', [MyProductController::class, 'disactive_product_by_seller']);
    // عرض الخدمة
    Route::get('/product/{id}', [MyProductController::class, 'product']);
    // تعيين الخدمة
    Route::get('/{slug}', [MyProductController::class, 'review']);
});

/**
 *  مسار لعرض مشترياتي
 */
Route::middleware('auth:sanctum')->prefix('my_purchases')->group(function () {
    Route::get('/', [BuyerOrderController::class, 'index']);
    Route::get('/{id}', [BuyerOrderController::class, 'show']);
});

/********************************************************************** */
/**
 *  مسار لعرض مشترياتي
 */
Route::middleware('auth:sanctum')->prefix('my_sales')->group(function () {
    Route::get('/', [SellerOrderController::class, 'index']);
    Route::get('/{id}', [SellerOrderController::class, 'show']);
});
/******************************************************************** */

/********************************************************************** */

/**
 * مسار طلبات السحب
 */
Route::middleware('auth:sanctum')->prefix('withdrawals')->group(function () {
    Route::post('/paypal', [WithdrawalController::class, 'paypal']);
    Route::post('/wise', [WithdrawalController::class, 'wise']);
    Route::post('/bank', [WithdrawalController::class, 'bank']);
    Route::post('/bank_transfer', [WithdrawalController::class, 'bank_transfer']);
    Route::prefix('update')->group(function () {
        Route::post('/paypal', [WithdrawalController::class, 'update_paypal']);
        Route::post('/wise', [WithdrawalController::class, 'update_wise']);
        Route::post('/bank', [WithdrawalController::class, 'update_bank']);
        Route::post('/bank_transfer', [WithdrawalController::class, 'update_bank_transfer']);
    });
});

/********************************************************************** */
/**
 *  مسار لعرض محفظتي
 */
Route::middleware('auth:sanctum')->prefix('my_wallet')->group(function () {
    Route::get('/', [WalletController::class, 'index']);
});
/******************************************************************** */

/***********************مسارات استعادة كلمة المرور ******************** */
Route::post('/password/forget/sendResetLink', [ForgetPasswordController::class, 'send_token']);
Route::post('/password/forget/verify', [ForgetPasswordController::class, 'verify_token']);
Route::post('/password/forget/reset', [ForgetPasswordController::class, 'reset_password']);

/* -------------------------------------------------------------------------- */
/*                             مسارات  الاشعارات                            */
/* -------------------------------------------------------------------------- */

Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/{id}', [NotificationController::class, 'show']);
    Route::post('/markAllAsRead', [NotificationController::class, 'markAllAsRead']);
    Route::post('/{id}/markAsRead', [NotificationController::class, 'markAsRead']);
});

// تسجيل الدخول بواسطة مواقع تواصل الاجتماعي
Route::post('/login/{provider}', [LoginController::class, 'handleProviderCallback']);
// التسجيل بواسطة التواصل الاجتماعي
Route::post('/register', [RegisterController::class, 'register']);
// التححق من الايميل
Route::post('/email/verify', [RegisterController::class, 'verifyEmail']);
// ادخال رمز التحقق
Route::post('/email/resend', [RegisterController::class, 'resend_verify_code']);
/* -------------------------------------------------------------------------- */

/* -------------------------------------------------------------------------- */
/*                             مسارات الملف الشخصي                            */
/* -------------------------------------------------------------------------- */

Route::prefix('profiles')->group(function () {
    // انشاء المرحلة الاولى من البروفايل
    Route::post('/step_one', [ProfileController::class, 'step_one']);
    // انشاء المرحلة الثانية من البروفايل
    Route::post('/step_two', [ProfileController::class, 'step_two']);
    // انشاء المرحلة الثالثة من البروفايل
    Route::post('/step_three', [ProfileController::class, 'step_three']);
    // اظهار البروفايل
    Route::get('/{username}', [ProfileController::class, 'show']);
});


/* -------------------------------------------------------------------------- */
/*                         مسارات الملف الشخصي البائع                         */
/* -------------------------------------------------------------------------- */

Route::prefix('sellers')->group(function () {
    // اضافة بائع جديد
    Route::post('/store', [SellerController::class, 'store']);
    // اضافة تفاصيل بروفايل البائع
    Route::post('/detailsStore', [SellerController::class, 'detailsStore']);
    // اضافة المرحلة الاولى من بروفايل البائع
    Route::post('/step_one', [SellerController::class, 'step_one']);
    // اضافة المرحلة الثانية من بروفايل البائع
    Route::post('/step_two', [SellerController::class, 'step_two']);
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
    // رفع الصورة البارزة
    Route::post('/{id}/upload-thumbnail-step-four', [InsertProductContoller::class, 'upload_thumbnail']);
    // رفع الصور العرض
    Route::post('/{id}/upload-galaries-step-four', [InsertProductContoller::class, 'upload_galaries']);
    // المحلة الرابعة
    Route::post('/{id}/product-step-four', [InsertProductContoller::class, 'storeStepFour']);
    // المحلة الخامسة
    Route::post('/{id}/product-step-five', [InsertProductContoller::class, 'storeStepFive']);
    // حذف الخدمة
    Route::post('/{id}/deleteProduct', DeleteProductController::class);
    // إضافة محادثة للخدمة
    Route::post('/{id}/conversations/create', [ConversationController::class, 'product_conversation_store'])->middleware('auth:sanctum');
});

/* -------------------------------------------------------------------------- */
/*                          مسار رابط المختصر للخدمة                          */
/* -------------------------------------------------------------------------- */
Route::get('/s/{code}', ShortenerController::class);


/* -------------------------------------------------------------------------- */
/*                             المحادثات والرسائل                             */
/* -------------------------------------------------------------------------- */

Route::prefix('conversations')->middleware('auth:sanctum')->group(function () {
    // عرض المحادثات
    Route::get('/', [ConversationController::class, 'index']);
    // اظهار المحادثة
    Route::get('/{id}', [ConversationController::class, 'show']);
    // اضافة المحادثة
    Route::post('/store', [ConversationController::class, 'store']);
    // ارسال الرسالة
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
    //Route::post('/store', [OrderController::class, 'create_order_with_items']);
    Route::post('/store', [OrderController::class, 'create_order_with_items']);
    /* ------------------ مسارات المعاملة بين البائع و المشتري ------------------ */
    Route::prefix('items')->group(function () {
        // اظهار الطلبية الواحدة
        Route::get('/{id}/show_item', [ItemController::class, 'show']);
        // قبول الطلبية من قبل البائع
        Route::post('/{id}/item_accepted_by_seller', [ItemController::class, 'item_accepted_by_seller']);
        // رفض الطلبية من قبل البائع
        Route::post('/{id}/item_rejected_by_seller', [ItemController::class, 'item_rejected_by_seller']);
        // الغاء الطلبية من قبل المشتري
        Route::post('/{id}/item_cancelled_by_buyer', [ItemController::class, 'item_cancelled_by_buyer']);
        // الغاء الطلبية من قبل البائع
        Route::post('/{id}/item_cancelled_by_seller', [ItemController::class, 'item_cancelled_by_seller']);
        // رفع و تسليم المشروع من قبل البائع
        Route::post('/{id}/dilevered_by_seller', [ItemController::class, 'dilevered_by_seller']);
        // قبول المشروع من قبل المشتري
        Route::post('/{id}/accepted_delivery_by_buyer', [ItemController::class, 'accepted_delivery_by_buyer']);
        // عرض الالغاء طلب الخدمة
        Route::get('/{id}/display_item_rejected', [ItemController::class, 'display_item_rejected']);
        // طلب الالغاء الخدمة من قبل المشتري
        Route::post('/{id}/request_cancel_item_by_buyer', [ItemController::class, 'request_cancel_item_by_buyer']);
        //  قبول طلب الالغاء الخدمة من قبل البائع
        Route::post('/{id}/accept_cancel_request_by_seller', [ItemController::class, 'accept_cancel_request_by_seller']);
        //  رفض طلب الالغاء الخدمة من قبل البائع
        Route::post('/{id}/reject_cancel_request_by_seller', [ItemController::class, 'reject_cancel_request_by_seller']);
        // حل النزاع بين الطرفين في حالة الغاء الطلبية
        Route::post('/{id}/resolve_the_conflict_between_them_in_rejected', [ItemController::class, 'resolve_the_conflict_between_them_in_rejected']);
        // طلب تعديل الخدمة من قبل المشتري
        Route::post('/{id}/request_modified_by_buyer', [ItemController::class, 'request_modified_by_buyer']);
        // قبول تعديل الخدمة من قبل المشتري
        Route::post('/{id}/accept_modified_by_seller', [ItemController::class, 'accept_modified_by_seller']);
        // رفض تعديل الخدمة من قبل المشتري
        Route::post('/{id}/reject_modified_by_seller', [ItemController::class, 'reject_modified_by_seller']);
        // حل النزاع بين الطرفين في حالة الغاء الطلبية
        Route::post('/{id}/resolve_the_conflict_between_them_in_modified', [ItemController::class, 'resolve_the_conflict_between_them_in_modified']);
        // إضافة محادثة للخدمة
        Route::post('/{id}/conversations/create', [ConversationController::class, 'item_conversation_store'])->middleware('auth:sanctum');
        // تقييم الخدمة
        Route::post('/{id}/rating', [RatingController::class, 'rate']);
    });
});

/* -------------------------------------------------------------------------- */
/*                            مسارات واجهة المستخدم                           */
/* -------------------------------------------------------------------------- */

// عرض كل التصنيفات
Route::get('/categories', [FrontEndController::class, 'get_all_categories']);
// عرض التصنيفات الرئيسية
Route::get('/get_categories', [FrontEndController::class, 'get_categories']);
// عرض التصنيفات الفرعية
Route::get('/get_categories/{id}', [FrontEndController::class, 'get_subcategories']);
// عرض التصنيف الفرعي مع خدماته
Route::get('/get_products_subcategory/{id}', [FrontEndController::class, 'get_products_by_subcategory']);
// عرض الخدمة الواحدة
Route::get('product/{slug}', [FrontEndController::class, 'show']);

// ارسال رسالة الى لوحة التحكم
Route::post('/contactus', [FrontEndController::class, 'send_to_dashboad']);

Route::get('tags/filter', [TagController::class, 'filter']);

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

Route::prefix('/purchase')->group(function () {
    Route::post('/paypal/approve', [OrderController::class, 'cart_approve']);
    Route::post('/paypal/charge', [OrderController::class, 'paypal_charge']);
    Route::post('/stripe/charge', [OrderController::class, 'stripe_charge']);
    Route::post('/wallet/charge', [OrderController::class, 'wallet_charge']);
});

/*
Route::get('users', function () {
    $basic  = new \Vonage\Client\Credentials\Basic("b5c4c461", "8zJCbf47nkL2bc6k");
    $client = new \Vonage\Client(new \Vonage\Client\Credentials\Container($basic));
    $response = $client->sms()->send(
        new \Vonage\SMS\Message\SMS("213554668588", 'SHAHDAH', 'لقد تم ارسال رسالة تجريبية إلى رقمك')
    );

    $message = $response->current();

    if ($message->getStatus() == 0) {
        echo "The message was sent successfully\n";
    } else {
        echo "The message failed with status: " . $message->getStatus() . "\n";
    }
});
 */
Route::get('testing', function () {
    $data = MoneyActivity::first();
    return response()->json($data, 200);
});
