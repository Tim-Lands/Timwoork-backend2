<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RejectProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * index => عرض جميع الخدمات
     *
     * @return void
     */
    public function index(): JsonResponse
    {
        // جلب جميع الخدمات
        $products = Product::selection()->where('is_completed', 1)
        ->with(['subcategory', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * get_all_messages_for_rejected_product => رسائل رفض الخدمات
     *
     * @return void
     */
    public function get_all_messages_for_rejected_product()
    {
        $messages_rejected = RejectProduct::selection()->get();
        return response()->success(__("messages.oprations.get_all_data"), $messages_rejected);
    }
    /**
     * show => id  دالة جلب الخدمة معينة بواسطة المعرف
     *
     * @param  mixed $id
     * @return void
     */
    public function show(mixed $id)
    {
        //slug  جلب العنصر بواسطة
        $product = Product::Selection()
                    ->where('is_completed', 1)
                    ->whereId($id)
                    ->with(['subcategory', 'profileSeller'])->first();
        // شرط اذا كان العنصر موجود
        if (!$product) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
        }

        // اظهار العنصر
        return response()->success(__("messages.oprations.get_data"), $product);
    }

    /**
     * show_product_for_scan => اظهار الخدمة من اجل قبولها او رفضها
     *
     * @param  mixed $id
     * @return void
     */
    public function show_product_for_checked($id)
    {
        // slug جلب الخدمة بواسطة
        $product = Product::selection()
                ->whereId($id)
                ->withOnly([
                    'subcategory' => function ($q) {
                        $q->select('id', 'parent_id', 'name_ar', 'name_en', 'name_fr')
                            ->with('category', function ($q) {
                                $q->select('id', 'name_ar', 'name_en', 'name_fr')
                                    ->without('subcategories');
                            })->withCount('products');
                    },
                    'developments' => function ($q) {
                        $q->select('id', 'title', 'price', 'duration', 'product_id');
                    },
                    'product_tag:id,name',
                    'ratings' => function ($q) {
                        $q->with('user.profile');
                    },
                    'galaries' => function ($q) {
                        $q->select('id', 'path', 'product_id');
                    },
                    'video' => function ($q) {
                        $q->select('id', 'product_id', 'url_video');
                    },
                    'profileSeller' => function ($q) {
                        $q->select('id', 'profile_id', 'number_of_sales', 'portfolio', 'profile_id', 'seller_badge_id', 'seller_level_id')
                            ->with([
                                'badge:id,name_ar,name_en,name_fr',
                                'level:id,name_ar,name_en,name_fr',
                                'profile' =>
                                function ($q) {
                                    $q->select('id', 'user_id', 'first_name', 'last_name', 'avatar', 'precent_rating', 'level_id', 'badge_id', 'country_id')
                                        ->with(['user' => function ($q) {
                                            $q->select('id', 'username', 'email', 'phone');
                                        },
                                           'badge:id,name_ar,name_en,name_fr',
                                           'level:id,name_ar,name_en,name_fr',
                                           'country'
                                          ])
                                        ->without('profile_seller');
                                }
                            ]);
                    }
                ])
                ->where('is_completed', 1)
                ->first();
        // فحص اذا كان يوجد هذا العنصر
        if (!$product) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
        }
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_data"), $product);
    }

    /**
     * productsActived
     *
     * @return JsonResponse
     */
    public function getRroductsActived(): JsonResponse
    {
        // جلب جميع الخدمات التي تم تنشيطها
        $products_actived = Product::selection()->productActive()->with(['category', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success(__("messages.dashboard.get_product_actived"), $products_actived);
    }

    /**
     * productsActived
     *
     * @return JsonResponse
     */
    public function getProductsRejected(): JsonResponse
    {
        // جلب جميع الخدمات التي تم رفضها
        $products_rejected = Product::selection()->productReject()->with(['category', 'profileSeller'])->get();
        // اظهار العناصر
        return response()->success(__("messages.dashboard.get_product_rejected"), $products_rejected);
    }
}
