<?php

use App\Http\Controllers\Dashboard\ActivedProductController;
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


// مسار تسجيل الدخول
Route::post('/login', [AuthController::class, 'login']);

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

// =============================== مسارات الخدمة ====================================
Route::prefix('products')->group(function () {
    // مسار العرض الخدمات
    Route::get('/', [ProductController::class, 'index']);
    // مسار العرض الخدمات التي تم تنشيطها
    Route::get('/active/status', [ProductController::class, 'getProductsActived']);
    // مسار العرض الخدمات التي تم تنشيطها
    Route::get('/reject/status', [ProductController::class, 'getProductsRejected']);
    // عرض الرسائل خدمات المرفوضة
    Route::get('/messages_rejected', [ProductController::class,'get_all_messages_for_rejected_product']);
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
    // مسار ارسال رسالة رفض الخدمة
    Route::post('/{id}/send_reject_product', RejectProductController::class);
    // استرجاع الخدمة المحذوفة
    Route::post('/{id}/restore_product_deleted', [ProductController::class, 'restore_product_deleted']);
    // حذف الخدمة نهائيا
    Route::post('/{id}/force_delete_product', [ProductController::class, 'force_delete_product']);
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
    // مسار انشاء عنصر جديد
    Route::post('/sent_to_client_by_email/{$id}', [ContactController::class, 'sent_to_client_by_email']);
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
