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
                    'icon'    => $category['icon'],
                    'products_count' => $category['subcategories']->sum('products_count')
                ];
        }

        // اظهار العناصر
        return response()->success('عرض تصنيفات الرئيسية', $data);
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
        $catagory = Category::find($id);
        if (!$catagory)
            return response()->error('هذا العنصر غير موجود', 403);
        // جلب التصنيفات الفرعية
        $subcategorie = Category::select('id', 'name_ar', 'icon')
            ->withCount('products')
            ->where('parent_id', $id)
            ->child()
            ->orderBy('products_count', 'desc')
            ->take(Category::SUBCATEGORY_DISPLAY)
            ->get();

        // اظهار العناصر
        return response()->success('عرض تصنيفات الفرعية', $subcategorie);
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
            ->with([
                'subcategory' => function ($q) {
                    $q->select('id', 'parent_id', 'name_ar',)
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
                    $q->select('id', 'path', 'url_video', 'product_id');
                },
                'profileSeller' => function ($q) {
                    $q->select('id', 'profile_id', 'number_of_sales', 'portfolio', 'profile_id', 'badge_id', 'level_id')
                        ->with(
                            'profile',
                            function ($q) {
                                $q->select('id', 'user_id', 'first_name', 'last_name', 'avatar', 'precent_rating')
                                    ->with('user', function ($q) {
                                        $q->select('id', 'username', 'email', 'phone', 'pm_last_four', 'trial_ends_at', 'pm_type');
                                    })
                                    ->without('profile_seller');
                            }
                        );
                }
            ])->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->first();
        // فحص اذا كان يوجد هذا العنصر
        if (!$product)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العناصر
        return response()->success('عرض خدمة', $product);
    }
}
