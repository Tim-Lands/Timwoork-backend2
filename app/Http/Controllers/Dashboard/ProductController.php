<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RejectProduct;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        ->with(['subcategory', 'profileSeller' => function ($q) {
            $q->select('id', 'profile_id')->with('profile', function ($q) {
                $q->select('id', 'full_name', 'user_id')->with('user:id,username');
            });
        }])
        ->latest()
        ->get();
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
    public function getProductsActived(): JsonResponse
    {
        // جلب جميع الخدمات التي تم تنشيطها
        $products_actived = Product::selection()->productActive()->with(['category', 'profileSeller'])
        ->latest()
        ->get();
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
        $products_rejected = Product::selection()->productReject()->with(['category', 'profileSeller'])
        ->latest()
        ->get();
        // اظهار العناصر
        return response()->success(__("messages.dashboard.get_product_rejected"), $products_rejected);
    }

    /**
     * products_soft_deleted => جلب الخدمات المحذوفة
     *
     * @return void
     */
    public function get_products_soft_deleted()
    {
        //استعلام جلب الخدمات المحذوفة
        $products = Product::selection()->with(['profileSeller'=> function ($q) {
            $q->select('id', 'profile_id')
            ->with('profile', function ($q) {
                $q->select('id', 'first_name', 'last_name', 'user_id')
                ->with('user:id,username')
                ->without('level', 'badge');
            })
            ->without('level', 'badge');
        },'subcategory'=> function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'name_fr')
            ->with('category:name_ar,name_en,name_fr');
        }])
        ->onlyTrashed()
        ->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $products);
    }

    /**
     * restore_product_deleted => استرجاع الخدمة المحذوفة
     *
     * @param  mixed $id
     * @return void
     */
    public function restore_product_deleted($id)
    {
        try {
            // جلب الخدمة المحذوفة
            $product = Product::where('id', $id)->onlyTrashed()->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            /* ------------------------- استعادة الخدمة المحذوفة ------------------------ */
            $product->restore();
            //رسالة نجاح العملية
            return response()->success(__("messages.oprations.restore_delete_success"), $product);
        } catch (Exception $ex) {
            return $ex;
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * force_delete_product => الحذف النهائي للخدمة
     *
     * @param  mixed $id
     * @return void
     */
    public function force_delete_product($id)
    {
        try {
            // جلب الخدمة المحذوفة
            $product = Product::where('id', $id)->onlyTrashed()->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // ============================== حذف الصور و المفات ==================================
            // حذف الصورة من مجلد
            if ($product->thumbnail) {
                Storage::has("products/thumbnails/{$product->thumbnail}") ? Storage::delete("products/thumbnails/{$product->thumbnail}") : '';
            }

            // جلب الصور مع الخدمة
            $get_galaries_images =  $product->whereId($id)->onlyTrashed()->with(['galaries' => function ($q) {
                $q->select('id', 'path', 'product_id')->get();
            }])->first()->galaries;

            // حذف الصور اذا وجدت فالمجلد
            if ($get_galaries_images) {
                foreach ($get_galaries_images as $key => $image) {
                    Storage::has("products/galaries-images/{$image['path']}") ? Storage::delete("products/galaries-images/{$image['path']}") : '';
                }
            }
            // ====================================================================================
            // ============================== حذف الخدمة ====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية حذف الخدمة
            $product->forceDelete();
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ==============================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.oprations.delete_success"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
