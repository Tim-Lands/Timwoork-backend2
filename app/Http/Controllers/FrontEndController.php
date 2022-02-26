<?php

namespace App\Http\Controllers;

use App\Models\Category;

use App\Models\Product;
use Illuminate\Http\JsonResponse;

class FrontEndController extends Controller
{
    /**
     * get_categories => دالة اظهار التصنيفات الرئيسية
     *
     * @return void
     */
    public function get_categories()
    {
        // جلب التصنيفات الرئيسية
        $categories = Category::Selection()->with('subcategories', function ($q) {
            $q->withCount('products');
        })->parent()->get();
        $data = [];
        // عمل لووب من اجل فرز التصنيفات الرئيسية مع عدد الخدمات التابعة لها
        foreach ($categories as $category) {
            $data[] =
                [
                    'id'      => $category['id'],
                    'name_ar' => $category['name_ar'],
                    'name_en' => $category['name_en'],
                    'name_fr' => $category['name_fr'],
                    'description_ar' => $category['description_ar'],
                    'description_en' => $category['description_en'],
                    'description_fr' => $category['description_fr'],
                    'parent_id' => $category['parent_id'],
                    'icon'    => $category['icon'],
                    'products_count' => $category['subcategories']->sum('products_count')
                ];
        }

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $data);
    }

    /**
     * get_subcategories => دالة اظهار التصنيفات الفرعية
     *
     * @param  mixed $id
     * @return void
     */
    public function get_subcategories(mixed $id): JsonResponse
    {
        // جلب التصنيف الرئيسي من اجل التحقق
        $catagory = Category::whereId($id);
        if (!$catagory->first()) {
            return response()->error(__("messages.errors.element_not_found"), 403);
        }
        // جلب التصنيفات الفرعية
        $subcategories = $catagory->selection()->with('subCategories', function ($q) {
            $q->selection()
            ->withCount('products')
            ->orderBy('id', 'asc')
            ->take(Category::SUBCATEGORY_DISPLAY)
            ->get();
        })->first();
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $subcategories);
    }


    /**
     * show => slug او  id  عرض الخدمة الواحدة بواسطة
     *
     * @param  mixed $slug
     * @return JsonResponse
     */
    public function show(mixed $slug): JsonResponse
    {
        // id او slug جلب الخدمة بواسطة
        $product = Product::selection()
            ->whereSlug($slug)
            ->orWhere('id', $slug)
            ->withOnly([
                'subcategory' => function ($q) {
                    $q->select('id', 'parent_id', 'name_ar', )
                        ->with('category', function ($q) {
                            $q->select('id', 'name_ar')
                                ->without('subcategories');
                        })->withCount('products');
                },
                'developments' => function ($q) {
                    $q->select('id', 'title', 'price', 'duration', 'product_id');
                },
                'product_tag',
                'ratings' => function ($q) {
                    $q->with('user.profile');
                },
                'galaries' => function ($q) {
                    $q->select('id', 'path', 'product_id');
                },
                'file' => function ($q) {
                    $q->select('id', 'path', 'product_id');
                },
                'video' => function ($q) {
                    $q->select('id', 'product_id', 'url_video');
                },
                'profileSeller' => function ($q) {
                    $q->select('id', 'profile_id', 'number_of_sales', 'portfolio', 'profile_id', 'seller_badge_id', 'seller_level_id')
                        ->with([
                            'profile' =>
                            function ($q) {
                                $q->select('id', 'user_id', 'first_name', 'last_name', 'avatar', 'precent_rating')
                                    ->with(['user' => function ($q) {
                                        $q->select('id', 'username', 'email', 'phone');
                                    }, 'badge', 'level', 'country'])
                                    ->without('profile_seller');
                            },
                            'level',
                            'badge'
                        ]);
                }
            ])
            ->where('is_completed', 1)
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->first();
        // فحص اذا كان يوجد هذا العنصر
        if (!$product) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), 403);
        }
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_data"), $product);
    }

    /**
     * get_all_categories => دالة عرض كل التصنيفات
     *
     * @return JsonResponse
     */
    public function get_all_categories()
    {
        // جلب جميع الاصناف الرئيسة و الاصناف الفرعية عن طريق التصفح
        $categories = Category::Selection()->with(['subcategories' => function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'parent_id', 'icon');
        }])->parent()->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $categories);
    }
}
