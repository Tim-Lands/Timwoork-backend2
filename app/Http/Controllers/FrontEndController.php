<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        $subcategories = $catagory->select('id', 'name_ar', 'name_en', 'name_fr')
        ->with('subCategories', function ($q) {
            $q->select('id', 'name_ar', 'name_en', 'name_fr')
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
                    $q->select('id', 'parent_id', 'name_ar', 'name_en', 'name_fr')
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
                                    }, 'badge:id,name_ar,name_en,name_fr', 'level:id,name_ar,name_en,name_fr', 'country'])
                                    ->without('profile_seller');
                            },
                            'level:id,name_ar,name_en,name_fr',
                            'badge:id,name_ar,name_en,name_fr'
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
            $q->select('id', 'name_ar', 'name_en', 'name_fr', 'parent_id', 'icon');
        }])->parent()->get();

        // اظهار العناصر
        return response()->success(__("messages.oprations.get_all_data"), $categories);
    }

    /**
     * get_products_by_subcategory => جلب الخدمات التابعة لهذا التصنيف
     *
     * @param  mixed $id
     * @return void
     */
    public function get_products_by_subcategory($id)
    {
        // جلب التصنيف الرئيسي من اجل التحقق
        $subcatagory = Category::select('id', 'name_ar', 'name_en', 'name_fr', 'parent_id', 'icon')
        ->whereId($id)
        ->orWhere('slug', $id)
        ->child()
        ->with(['products' => function ($q) {
            $q->select('id', 'profile_seller_id', 'category_id', 'title', 'price', 'thumbnail', 'count_buying', 'duration')
            ->where('is_completed', 1)
            ->where('status', 1)
            ->where('is_active', 1)
            ->where('is_vide', 0)
            ->with('profileSeller', function ($q) {
                $q->select('id', 'profile_id')->without('level', 'badge')
                ->with('profile', function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'user_id')
                    ->with('user:id,username')->without('level', 'badge');
                });
            });
        }])->first();
        // التحقق من التصنيف انه موجود
        if (!$subcatagory) {
            return response()->error(__("messages.errors.element_not_found"), 403);
        }
        // اظهار العناصر
        return response()->success(__("messages.oprations.get_data"), $subcatagory);
    }



    /**
     * send_to_dashboad => دالة ارسال الرسالة الى لوحة التحكم
     *
     * @param  mixed $request
     * @return void
     */
    public function send_to_dashboad(ContactRequest $request)
    {
        try {
            $contact = Contact::selection()
                                        ->where(function ($query) use ($request) {
                                            $query->where('email', $request->email)
                                                  ->orWhere('ip_client', $request->ip());
                                        })
                                        ->where('date_expired', '>', Carbon::now()->toDateTimeString())
                                        ->first();
            if ($contact) {
                return response()->error(__("messages.contact.cannot_sent_before_48"), Response::HTTP_BAD_REQUEST);
            }
            // انشاء مصفوفة من اجل ارسال المعلومات
            $data_contact = [
                "subject"         => $request->subject,
                "email"           => $request->email,
                "full_name"       => $request->full_name,
                "type_message"    => $request->type_message,
                "message"         => $request->message,
                "date_expired"    => Carbon::now()->addDays(2)->toDateTimeString(),
                "ip_client"       => $request->ip()
            ];

            // شرط اذا كان يوجد رابط
            if ($request->has('url')) {
                // شرط ان يكون الرابط يحتوي على غوغل درايف او دروب بوكس
                if (str_contains($request->url, Contact::URL_GOOGLE_DRIVE) || str_contains($request->url, Contact::URL_DROPBOX)) {
                    $data_contact["url"] = $request->url;
                } else {
                    // رسالة خطأ
                    return response()->error(__("messages.contact.not_found_url"), Response::HTTP_BAD_REQUEST);
                }
            }
            /* ------------------------------- عملية ارسال ------------------------------ */

            DB::beginTransaction();
            // عملية انشاء الرسالة
            Contact::create($data_contact);
            // ارسال اشعار للوحة التحكم
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            // رسالة نجاح العملية
            return response()->success(__("messages.contact.success_message_contact"));
        } catch (Exception $ex) {
            return $ex;
            DB::rollBack();
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
}
