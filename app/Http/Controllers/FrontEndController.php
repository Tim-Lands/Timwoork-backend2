<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class FrontEndController extends Controller
{
    /**
     * get_categories_subcategories_porducts => دالة عرض تصنيفات الرئيسية و الفرعية مع عدد التصنيفات الفرعية و الخدمات 
     *
     * @return void
     */
    public function get_categories_subcategories_porducts()
    {
        // جلب جميع الاصناف الرئيسة و الاصناف الفرعية عن طريق التصفح
        $categories = Category::Selection()->withCount('subcategories')->with(['subcategories' => function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'parent_id', 'icon')->withCount('products');
        }])->parent()->get();
        $data = [];
        foreach ($categories as $sub) {
            $data[] = [
                'category' => [
                    "name_ar"           => $sub['name_ar'],
                    "name_en"           => $sub['name_en'],
                    "name_fr"           => $sub['name_fr'],
                    "slug"              => $sub['slug'],
                    "subcategories_count" => $sub['subcategories_count'],
                    "icon"              => $sub['icon'],
                    'subcategories' => $sub['subcategories']->take(6)
                ],
            ];
        }
        // اظهار العناصر
        return response()->success('عرض كل تصنيفات الرئيسية و الفرعية', $data);
    }
}
