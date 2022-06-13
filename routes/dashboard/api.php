<?php

use App\Http\Controllers\Dashboard\ActivedProductController;
use App\Http\Controllers\Dashboard\ActivityController;
use App\Http\Controllers\Dashboard\AuthController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\CountryController;
use App\Http\Controllers\Dashboard\SubCategoryController;
use App\Http\Controllers\Dashboard\LevelController;
use App\Http\Controllers\Dashboard\BadgeController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\StatisticContoller;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\SkillController;
use App\Http\Controllers\Dashboard\LanguageController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\RejectProductController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\SellerBadgeController;
use App\Http\Controllers\Dashboard\SellerLevelController;
use App\Http\Controllers\Dashboard\TypePaymentController;
use App\Http\Controllers\Dashboard\UserContoller;
use App\Http\Controllers\ExternalAccountRatingController;
use App\Http\Controllers\ExternalRatingController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DashBoard API Routes
|--------------------------------------------------------------------------
|
*/

Route::group(['middleware' => ['XSS']], function () {

    // مسار تسجيل الدخول
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        // =======================  مسارات التسجيل و التسجيل دخول الtype.adminمدير ======================
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // =================================== مسار الاحصائيات ===============================
        Route::get('/', StatisticContoller::class);
        // =============================== مسارات التصنيف ====================================
        Route::prefix('categories')->group(function () {
            // مسار العرض
            Route::get('/', [CategoryController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [CategoryController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [CategoryController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [CategoryController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [CategoryController::class, 'delete']);
        });
        // ================================ مسارات المستخدمين ====================================
        Route::prefix('users')->group(function () {
            // مسار العرض
            Route::get('/', [UserContoller::class, 'get_users']);
            // عرض المستخدمين المحظورين
            Route::get('/get_users_banned', [UserContoller::class, 'get_user_banned']);
            // عرض المستخدمين الغير المحظورين
            Route::get('/get_users_unbanned', [UserContoller::class, 'get_user_unbanned']);
            // مسار انشاء عنصر جديد
            Route::get('/{id}', [UserContoller::class, 'show']);
            // حظر المستخدم
            Route::post('/{id}/ban', [UserContoller::class, 'user_ban']);
            // فك حظر المستخدم
            Route::post('/{id}/unban', [UserContoller::class, 'user_unban']);
        });

        // =============================== مسارات التصنيف الفرعي ====================================
        Route::prefix('subcategories')->group(function () {

            // مسار عرض عنصر من اجل انشاء
            Route::get('/create', [SubCategoryController::class, 'create']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [SubCategoryController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [SubCategoryController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [SubCategoryController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [SubCategoryController::class, 'delete']);
        });

        // =============================== مسارات المستوى ==================================

        Route::prefix('levels')->group(function () {
            // مسار العرض
            Route::get('/', [LevelController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [LevelController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [LevelController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [LevelController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [LevelController::class, 'delete']);
        });

        // =============================== مسارات المستوى البائع ==================================

        Route::prefix('levels_sellers')->group(function () {
            // مسار العرض
            Route::get('/', [SellerLevelController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [SellerLevelController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [SellerLevelController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [SellerLevelController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [SellerLevelController::class, 'delete']);
        });

        // =============================== مسارات الشارة ====================================

        Route::prefix('badges')->group(function () {
            // مسار العرض
            Route::get('/', [BadgeController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [BadgeController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [BadgeController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [BadgeController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [BadgeController::class, 'delete']);
        });
        // =============================== مسارات الشارة البائع ====================================

        Route::prefix('badges_sellers')->group(function () {
            // مسار العرض
            Route::get('/', [SellerBadgeController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [SellerBadgeController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [SellerBadgeController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [SellerBadgeController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [SellerBadgeController::class, 'delete']);
        });
        // =============================== مسارات الخدمة ====================================
        Route::prefix('products')->group(function () {
            // مسار العرض الخدمات
            Route::get('/', [ProductController::class, 'index']);
            // مسار العرض الخدمات التي تم تنشيطها
            Route::get('/active/status', [ProductController::class, 'getProductsActived']);
            // مسار العرض الخدمات التي تم تنشيطها
            Route::get('/reject/status', [ProductController::class, 'getProductsRejected']);
            // عرض الرسائل خدمات المرفوضة
            Route::get('/messages_rejected', [ProductController::class, 'get_all_messages_for_rejected_product']);
            // جلب الخدمات المحذوفة
            Route::get('/get_products_soft_deleted', [ProductController::class, 'get_products_soft_deleted']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [ProductController::class, 'show']);
            // مسار جلب عنصر الواحد من اجل الفحص
            Route::get('show_product_checked/{id}', [ProductController::class, 'show_product_for_checked']);
            // مسار تنشيط الخدمة
            Route::post('/{id}/activeProduct', [ActivedProductController::class, 'activeProduct']);
            // مسار رفض الخدمة
            Route::post('/{id}/rejectProduct', [ActivedProductController::class, 'rejectProduct']);
            Route::post('/{id}/disactive_product', [ActivedProductController::class, 'disactiveProduct']);

            // مسار ارسال رسالة رفض الخدمة
            Route::post('/{id}/send_reject_product', RejectProductController::class);
            // استرجاع الخدمة المحذوفة
            Route::post('/{id}/restore_product_deleted', [ProductController::class, 'restore_product_deleted']);
            // حذف الخدمة
            Route::post('/{id}/delete', [ProductController::class, 'delete']);
            // حذف الخدمة نهائيا
            Route::post('/{id}/force_delete_product', [ProductController::class, 'force_delete_product']);
            // مسار التعديل المرحلة الاولى
            Route::post('/{id}/step_one', [ProductController::class,'product_step_one']);
            // مسار التعديل المرحلة الثانية
            Route::post('/{id}/step_two', [ProductController::class,'product_step_two']);
            // مسار التعديل المرحلة الثالثة
            Route::post('/{id}/step_three', [ProductController::class,'product_step_three']);
            // مسار التعديل المرحلة الرابعة
            Route::post('/{id}/step_four', [ProductController::class,'product_step_four']);
            // مسار التعديل على الصورة البارزة
            Route::post('/{id}/upload_thumbnail', [ProductController::class,'upload_thumbnail']);
            // مسار التعديل المعرض
            Route::post('/{id}/delete_galary', [ProductController::class,'delete_one_galary']);
            // مسار حذف الصورة من المعرض
            Route::post('/{id}/upload_galaries', [ProductController::class,'upload_galaries']);
        });

        // =============================== مسارات الوسم ====================================
        Route::prefix('tags')->group(function () {
            // مسار العرض
            Route::get('/', [TagController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [TagController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [TagController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [TagController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [TagController::class, 'delete']);
        });
        // =============================== مسارات الدولة ==================================

        Route::prefix('countries')->group(function () {
            // مسار العرض
            Route::get('/', [CountryController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [CountryController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [CountryController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [CountryController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [CountryController::class, 'delete']);
        });

        // =============================== مسارات الوسم ====================================
        Route::prefix('skills')->group(function () {
            // مسار العرض
            Route::get('/', [SkillController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [SkillController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [SkillController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [SkillController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [SkillController::class, 'delete']);
        });

        // =============================== مسارات الوسم ====================================
        Route::prefix('languages')->group(function () {
            // مسار العرض
            Route::get('/', [LanguageController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [LanguageController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [LanguageController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [LanguageController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [LanguageController::class, 'delete']);
        });
        // =============================== مسار اتصل بنا ====================================
        Route::prefix('contacts')->group(function () {
            //  مسار العرض كل الرسائل
            Route::get('/', [ContactController::class, 'index']);
            // عرض الرسالة الواحدة
            Route::get('/{id}', [ContactController::class, 'show']);
            // مسار انشاء عنصر جديد
            Route::post('/sent_to_client_by_email/{id}', [ContactController::class, 'sent_to_client_by_email']);
            // مسار الشكاوي
            Route::get('/get_messages_complaints', [ContactController::class, 'get_messages_complaints']);
            //مسار الاستفسارات
            Route::post('/get_messages_enquiries', [ContactController::class, 'get_messages_enquiries']);
        });

        /* ------------------------------- مسار طلبيات ------------------------------ */
        Route::prefix('orders')->group(function () {
            //  عرض كل الطلبيات
            Route::get('/', [OrderController::class, 'index']);
            // عرض طلبية الواحدة
            Route::get('/{id}', [OrderController::class, 'show']);
            // عرض عنصر من عناصر الطلبية
            Route::get('item/{id}', [OrderController::class, 'get_order_item']);
        });

        /* ------------------------------- مسار طلبيات ------------------------------ */
        Route::prefix('withdrawals')->group(function () {
            //  عرض كل الطلبيات
            Route::get('/', [WithdrawalController::class, 'index']);
            // عرض طلبية الواحدة
            Route::get('/{id}', [WithdrawalController::class, 'show']);
            // عرض عنصر من عناصر الطلبية
            Route::post('/{id}/accept', [WithdrawalController::class, 'accept']);
            Route::post('/{id}/cancel', [WithdrawalController::class, 'cancel']);
        });


        /* ------------------------------ مسار البوابات ----------------------------- */
        Route::prefix('types_payments')->group(function () {
            // مسار العرض
            Route::get('/', [TypePaymentController::class, 'index']);
            // مسار انشاء عنصر جديد
            Route::post('/store', [TypePaymentController::class, 'store']);
            // مسار جلب عنصر الواحد
            Route::get('/{id}', [TypePaymentController::class, 'show']);
            // مسار التعديل على العنصر
            Route::post('/{id}/update', [TypePaymentController::class, 'update']);
            // مسار حذف العنصر
            Route::post('/{id}/delete', [TypePaymentController::class, 'delete']);
            // مسار تنشيط البوابة
            Route::post('/{id}/active_payment', [TypePaymentController::class, 'active_payment']);
            // مسار تعطيل البوابة
            Route::post('/{id}/disactive_payment', [TypePaymentController::class, 'disactive_payment']);
        });
        Route::prefix('activities')->group(function () {
            // مسار العرض جميع الاشعارات
            Route::get('/get_all_notifications', [ActivityController::class, 'get_all_notifications']);
            //  مسار جلب جميع المحادثات
            Route::get('/get_all_conversations', [ActivityController::class, 'get_all_conversations']);
            // مسار جلب المعاملات المالية
            Route::get('/all_financial_transactions', [ActivityController::class, 'all_financial_transactions']);
            // مسار جلب المحادثة الواحدة
            Route::get('/{id}/conversation', [ActivityController::class, 'get_conversation']);
            // حذف المحادثة
            Route::post('conversation/{id}/delete', [ActivityController::class, 'get_conversation']);
            // التعديل على الرسالة
            Route::post('/message/{id}/update', [ActivityController::class, 'update_message']);
            // مسار تنشيط البوابة
            Route::post('/message/{id}/delete', [ActivityController::class, 'delete_message']);
        });

        Route::prefix('external_rating')->group(function () {
            //  عرض كل الطلبيات
            Route::get('/', [ExternalRatingController::class, 'index']);
            // عرض طلبية الواحدة
            Route::get('/{id}', [ExternalRatingController::class, 'show']);
            // عرض عنصر من عناصر الطلبية
            Route::post('/{id}/accept', [ExternalRatingController::class, 'accept']);
            Route::post('/{id}/cancel', [ExternalRatingController::class, 'cancel']);
        });

        Route::prefix('external_account_rating')->group(function () {
            //  عرض كل الطلبيات
            Route::get('/', [ExternalAccountRatingController::class, 'index']);
            // عرض طلبية الواحدة
            Route::get('/{id}', [ExternalAccountRatingController::class, 'show']);
            // عرض عنصر من عناصر الطلبية
            Route::post('/{id}/accept', [ExternalAccountRatingController::class, 'accept']);
            Route::post('/{id}/cancel', [ExternalAccountRatingController::class, 'cancel']);
        });
    });
});
