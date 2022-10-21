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
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\Dashboard\CountryController as DashboardCountryController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\Dashboard\UserContoller;
use App\Http\Controllers\ExternalAccountRatingController;
use App\Http\Controllers\ExternalRatingController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\FrontEndController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\NotificationController;
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
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::post('/detectLang', [FrontEndController::class,'detectLang']);
Route::get('/currency_values', [CurrencyController::class,'send_currency_values']);
Route::get('phone_codes', [CountryController::class, 'get_phone_codes']);
Route::get('/countries', [DashboardCountryController::class, 'get_countries'])->middleware('auth:sanctum', 'abilities:user');

Route::group(['middleware' => ['XSS','language']], function () {
    Route::get('/currency', [CurrencyController::class, 'index']);
    Route::get('/get_countries', [CountryController::class, 'index']);
    # code...
    Broadcast::routes(['middleware' => ['auth:sanctum' ,'abilities:user']]);
    // send data currency to frontend in pusher
    Route::get('/send_currency', [CurrencyController::class, 'send_currency']);
    // مسار الرابط
    Route::fallback(function () {
        return abort(403);
    });
    // مسار التسجيل الدخول
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttleLogin:3,1');
    /* -------------------------------------------------------------------------- */
    /*                                 Auth Routes                                */
    /* -------------------------------------------------------------------------- */
    Route::middleware('auth:sanctum', 'abilities:user')->group(function () {
        Route::prefix('me')->group(base_path('routes/me.php')); 

        Route::post('/logout_user', [LoginController::class, 'logout_user']);
        Route::post('/logout_all', [LoginController::class, 'logout_all']);
        Route::post('/{user}/online', [UserStatusController::class, 'online']);
        Route::post('/{user}/offline', [UserStatusController::class, 'offline']);
        Route::post('/mode', DarkModeController::class);
        Route::post('/password/change', ChangePasswordController::class);
    });
    /* -------------------------------------------------------------------------- */
    /*                             مسارات خدمات البائع                            */
    /* -------------------------------------------------------------------------- */
   /*  Route::prefix('my_products')->middleware(['auth:sanctum', 'abilities:user'])->group(function () {
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
    }); */

    /**
     *  مسار لعرض مشترياتي
     */
  
    /******************************************************************** */

    /********************************************************************** */

    /**
     * مسار طلبات السحب
     */
    Route::middleware('auth:sanctum', 'abilities:user')->prefix('withdrawals')->group(function () {
        Route::get('/countries', [WithdrawalController::class, 'countries']);
        // حفظ حسابات البنكية
        Route::post('/store_paypal', [WithdrawalController::class, 'store_paypal']);
        Route::post('/store_wise', [WithdrawalController::class, 'store_wise']);
        Route::post('/store_bank', [WithdrawalController::class, 'store_bank']);
        Route::post('/store_bank_transfer', [WithdrawalController::class, 'store_bank_transfer']);
        // تعديل على حسابات البنكية
        Route::post('/update_paypal', [WithdrawalController::class, 'update_paypal']);
        Route::post('/update_wise', [WithdrawalController::class, 'update_wise']);
        Route::post('/update_bank', [WithdrawalController::class, 'update_bank']);
        Route::post('/update_bank_transfer', [WithdrawalController::class, 'update_bank_transfer']);
        // عمليات السحب
        Route::post('/withdrawal_paypal', [WithdrawalController::class, 'withdrawal_paypal']);
        Route::post('/withdrawal_wise', [WithdrawalController::class, 'withdrawal_wise']);
        Route::post('/withdrawal_bank', [WithdrawalController::class, 'withdrawal_bank']);
        Route::post('/withdrawal_bank_transfer', [WithdrawalController::class, 'withdrawal_bank_transfer']);
    });

    /********************************************************************** */
    /**
     *  مسار لعرض محفظتي
     */
    Route::middleware('auth:sanctum', 'abilities:user')->prefix('my_wallet')->group(function () {
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
    Route::post('/email/resend', [RegisterController::class, 'resend_verify_code'])->middleware('throttleLogin:3,1');
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

    Route::prefix('profile_seller')->group(function () {
        // اضافة بائع جديد
        Route::get('/',[SellerController::class,'index']);
        Route::post('/store', [SellerController::class, 'store']);
        // اضافة تفاصيل بروفايل البائع
        Route::put('/details', [SellerController::class, 'detailsStore']);
        // اضافة المرحلة الاولى من بروفايل البائع
        Route::post('/step_one', [SellerController::class, 'step_one']);
        // اضافة المرحلة الثانية من بروفايل البائع
        Route::post('/step_two', [SellerController::class, 'step_two']);
    });


    /* -------------------------------------------------------------------------- */
    /*                           مسارات انشاء خدمة جديدة                          */
    /* -------------------------------------------------------------------------- */
    /* Route::prefix('product')->middleware(['auth:sanctum', 'abilities:user'])->group(function () {
        // انشاء الخدمة
        Route::get('/store', [InsertProductContoller::class, 'store']);
        // حذف الصورة الواحدة من المعرض
        Route::post('/{id}/delete_galary', [InsertProductContoller::class, 'delete_one_galary']);
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
        Route::post('/{id}/conversations/create', [ConversationController::class, 'product_conversation_store'])->middleware('auth:sanctum', 'abilities:user');
    }); */

    /* -------------------------------------------------------------------------- */
    /*                          مسار رابط المختصر للخدمة                          */
    /* -------------------------------------------------------------------------- */
    Route::get('/s/{code}', ShortenerController::class);


    /* -------------------------------------------------------------------------- */
    /*                             المحادثات والرسائل                             */
    /* -------------------------------------------------------------------------- */

    Route::prefix('conversations')->middleware('auth:sanctum', 'abilities:user')->group(function () {
        // عرض المحادثات
        Route::get('/', [ConversationController::class, 'index']);
        // اظهار المحادثة
        Route::get('/{id}', [ConversationController::class, 'show']);
        // اضافة المحادثة
        Route::post('/', [ConversationController::class, 'store']);
        // ارسال الرسالة
        Route::put('/{conversation}/messages', [ConversationController::class, 'sendMessage']);
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

    Route::prefix('orders')->group(function () {
        // انشاء الطلبية و ارسال الطلبيات للبائعين
        //Route::post('/store', [OrderController::class, 'create_order_with_items']);
        Route::post('/', [OrderController::class, 'create_order_with_items']);
        /* ------------------ مسارات المعاملة بين البائع و المشتري ------------------ */
        Route::prefix('items')->group(function () {
            // اظهار الطلبية الواحدة
            Route::get('/{id}', [ItemController::class, 'show']); 
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
            Route::post('/{id}/conversations/', [ConversationController::class, 'item_conversation_store'])->middleware('auth:sanctum', 'abilities:user');
            // تقييم الخدمة
            Route::post('/{id}/rating', [RatingController::class, 'rate']);
        });
    });

    /* -------------------------------------------------------------------------- */
    /*                            مسارات واجهة المستخدم                           */
    /* -------------------------------------------------------------------------- */
    // عرض التصنيفات الرئيسية
    Route::prefix('categories')->group(function(){
    Route::get('/main', [FrontEndController::class, 'main_categories']);
    Route::get('/{id}/subcategories', [FrontEndController::class, 'get_subcategories']);
    Route::get('/', [FrontEndController::class, 'get_all_categories']);
    });
    
    Route::prefix('products')->group(function() {
        Route::get('/', FilterController::class);
    });
    // عرض التصنيفات الرئيسية
    // عرض التصنيفات من اجل عملية الاضافة
    Route::get('/get_categories_for_add_product', [FrontEndController::class, 'get_categories_for_add_product'])->middleware('auth:sanctum', 'abilities:user');
    // تحويل الاموال من المعلقة الى قابلة للسحب
    Route::get('withdrawal/change_amount', [FrontEndController::class, 'chage_amount_withdrawal']);
    // حذف الخدمات الفارغة
    Route::get('/products/vide', [FrontEndController::class, 'delete_product_vide']);

    // حذف الخدمات الفارغة
    Route::get('/email/expired', [FrontEndController::class, 'delete_code_verify_email']);

    // فتح الحسابات المحظورة عند انتهاء من وقت الحظر
    Route::get('/expired_unban_users', [UserContoller::class, 'expired_unban_users']);
    // عرض التصنيفات الفرعية

    // عرض التصنيفات الفرعية من اجل عملية الاضافة
    Route::get(
        '/get_categories_for_add_product/{id}',
        [FrontEndController::class, 'get_subcategories_for_add_product']
    )->middleware('auth:sanctum', 'abilities:user');

    // عرض التصنيف الفرعي مع خدماته
    Route::get('/get_products_subcategory/{id}', [FrontEndController::class, 'get_products_by_subcategory']);
    // عرض الخدمة الواحدة
    Route::get('product/{slug}', [FrontEndController::class, 'show']);

    // ارسال رسالة الى لوحة التحكم
    Route::post('/contactus', [FrontEndController::class, 'send_to_dashboad']);

    Route::get('tags/filter', [TagController::class, 'filter']);

    // مسار عملية الفلترة
  

    // مسار عملية البحث السريع

    Route::prefix('search')->group(function () {
        Route::get('/', SearchController::class);
    });

    // مسارات تقييم الخدمة بعد نجاح عملية البيع

    Route::prefix('rating')->group(function () {
        Route::post('/{id}/reply', [RatingController::class, 'reply']);
    });

    Route::prefix('external_rating')->group(function () {
        Route::post('/store', [ExternalRatingController::class, 'store']);
        Route::post('/{id}/update', [ExternalRatingController::class, 'update']);
    });

    Route::prefix('external_account_rating')->group(function () {
        Route::post('/store', [ExternalAccountRatingController::class, 'store']);
        Route::post('/{id}/update', [ExternalAccountRatingController::class, 'update']);
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
    /*Route::get('testing', function () {
        $data = MoneyActivity::first();
        return response()->json($data, 200);
    });*/
});
