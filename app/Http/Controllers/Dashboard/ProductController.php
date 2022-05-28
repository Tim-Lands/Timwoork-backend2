<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ImagesRequest;
use App\Http\Requests\Products\ProductStepFourRequest;
use App\Http\Requests\Products\ProductStepOneRequest;
use App\Http\Requests\Products\ProductStepThreeRequest;
use App\Http\Requests\Products\ProductStepTwoRequest;
use App\Http\Requests\Products\ThumbnailRequest;
use App\Models\Category;
use App\Models\Galary;
use App\Models\Product;
use App\Models\RejectProduct;
use App\Models\Tag;
use App\Models\Video;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
        // تصفح
        $paginate = request()->query('paginate') ? request()->query('paginate') : 10;
        // جلب جميع الخدمات
        $products = Product::selection()->where('is_completed', 1)
        ->with(['subcategory','galaries','product_tag','video','developments',
                 'profileSeller' => function ($q) {
                     $q->select('id', 'profile_id')->with('profile', function ($q) {
                         $q->select('id', 'full_name', 'user_id')->with('user:id,username');
                     });
                 }])
        ->filter('status', 'is_active')
        ->latest()
        ->paginate($paginate);
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
                    ->with(['subcategory', 'profileSeller','galaries','product_tag','video','developments'])
                    ->first();
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

    /**
     * product_step_one => تعديل على المرحلة الاولى للخدمة
     *
     * @return void
     */
    public function product_step_one($id, ProductStepOneRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 422);
            }
            // جلب التصنيف الفرعي
            $subcategory = Category::child()->where('id', $request->subcategory)->exists();
            // التحقق اذا كان موجود ام لا
            if (!$subcategory) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            $data = [
                'title'             => $request->title,
                'slug'              => $product->id .'-'.slug_with_arabic($request->title),
                'category_id'       =>  (int)$request->subcategory,
                'is_vide'           => 0,
            ];
            // جلب الوسوم من المستخدم
            $tag_request_values = array_values(array_map(function ($key) {
                return strtolower($key["value"]) ;
            }, $request->tags));
            // حلب الوسوم الموجودة داخل القواعد البيانات
            $tags = Tag::select('id', 'name')->whereIn('name', $tag_request_values)->get();

            // جلب الاسماء الوسوم مع فلترة تكرارها
            $get_name_tags = array_unique(array_map(function ($key) {
                return $key["name"];
            }, $tags->toArray()));

            // جلب المعرفات الملفترة و وضعهم في مصفوفة
            $ids = array_values(array_map(function ($key) {
                return $key['id'];
            }, array_filter($tags->toArray(), function ($key) {
                return strtolower($key["name"]) == $key["name"];
            })));
            // جلب الاسماء الجديدة الغير موجودة في قواعد البيانات
            $new_tags = array_values(array_diff($tag_request_values, $get_name_tags));
            /* --------------------- انشاء المرحلة الاولى في الخدمة --------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الاولى
            $product->update($data);
            // اضافة الكلمات المفتاحية الكلمات المفتاحية او الوسوم
            // شرط اذا كانت هناك كلمات مفتاحية جديدة
            if (!empty($new_tags)) {
                // عمل لوب من اجل اضافة كلمة جيدة
                foreach ($new_tags as $tag) {
                    // اضافة وسم جديد
                    $tag = Tag::create([
                        'name' => $tag,
                        'label' => $tag,
                        'value' => $tag
                    ]);
                    // وضع معرف الوسم في المصفوفة
                    $ids[] = $tag->id;
                }
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->sync($ids);
            } else {
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->sync($ids);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_one"), $product);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * product_step_two => تعديل على المرحلة الثانية للخدمة
     *
     * @param $id
     * @param ProductStepTwoRequest $request
     * @return void
     */
    public function product_step_two($id, ProductStepTwoRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثانية
            $data = [
                'price'           => (float)$request->price,
                'duration'        => (int)$request->duration
            ];
            // انشاء مصفوفة جديدة من اجل عملية اضافة تطويرات
            (object)$developments = [];
            // شرط اذا كانت هناك توجد تطورات
            if ($request->only('developments') != null) {
                // جلب المرسلات من العميل و وضعهم فالمصفوفة الجديدة
                foreach ($request->only('developments')['developments'] as $key => $value) {
                    $developments[] = $value;
                    // اذا كان السعر اكبر
                }
            }
            // =============== انشاء المرحلة الثانية في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثانية
            $product->update($data);
            // شرط اذا كانت هناط تطويرات من قبل
            if ($product->developments) {
                // حدف كل التطويرات
                $product->developments()->forceDelete();
            }

            // اضافة تطويرات جديدة
            $product->developments()->createMany($developments);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_two"), $product->load('developments'));
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * product_step_three => تعديل المرحلة الثالثة
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function product_step_three($id, ProductStepThreeRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثالثة
            $data = [
                'buyer_instruct'  => $request->buyer_instruct,
                'content'         => $request->content,
            ];
            // ============= انشاء المرحلة الثالثة في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_three"), $product);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * product_step_four => تعديل المرحلة الرابعة
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function product_step_four($id, ProductStepFourRequest $request)
    {
        try {

            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)->with(['galaries', 'video'])->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            if (count($product->galaries) == 0 || $product->thumbnail == null) {
                // رسالة خطأ
                return response()->error(__("messages.errors.upload_images"), 422);
            }

            // جلب رابط الفيديو
            $get_galaries_url_video =  $product->video;


            // ====================== انشاء المرحلة الرابعة في الخدمة =====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // تعديل على الخدمة
            //$product->update($data);
            // شرط اذا كانت هناك ارسال رابط في فيديو من قبل المستخدم
            if ($request->has('url_video')) {
                //return 1;
                // شرط اذا كانت توجد بيانات رابط الفيديو من قبل
                if ($get_galaries_url_video != null) {
                    // عملية التعديل على رابط الفيديو
                    $product->video()->update([
                        'url_video' => $request->url_video
                    ]);
                } else {
                    // انشاء رابط فيديو جديد
                    Video::create([
                        'url_video' => $request->url_video,
                        'product_id' => $product->id
                    ]);
                }
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_four"), $product->load('video'));
            // ========================================================
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * upload_thumbnail => رفع الصورة البارزة
     *
     * @param  mixed $id
     * @param  ThumbnailRequest $request
     * @return void
     */
    public function upload_thumbnail($id, ThumbnailRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع مصفوفة من اجل عملية التعديل
            $data_thumbnail = [];
            /* ------------------------- معالجة الصورة الامامية ------------------------- */
            $time = time();
            // شرط في حالة ما اذا كانت الصورة مرسلة من المستخدم
            if ($product->thumbnail) {
                // شرط اذا قام المستخدم بأرسال صورة الامامية
                if ($request->thumbnail) {
                    // حذف صورة السابقة
                    if (Storage::disk('do')->exists("products/thumbnails/{$product->thumbnail}")) {
                        Storage::disk('do')->delete("products/thumbnails/{$product->thumbnail}");
                    }
                    //Storage::delete("products/thumbnails/{$product->thumbnail}");
                    // جلب الصورة من المرسلات
                    $thumbnailPath = $request->file('thumbnail');
                    // وضع اسم جديد للصورة
                    $thumbnailName = "tw-thumbnail-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                    // رفع الصورة الامامية للخدمة
                    //Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                    $thumbnailPath->storePubliclyAs('products/thumbnails', $thumbnailName, 'do');
                    // وضع اسم الصورة في المصفوفة

                    $data_thumbnail['thumbnail'] = $thumbnailName;
                }
            } elseif ($request->thumbnail) {
                // جلب الصورة من المرسلات
                $thumbnailPath = $request->file('thumbnail');
                // وضع اسم جديد للصورة
                $thumbnailName = "tw-thumbnail-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                // رفع الصورة الامامية للخدمة
                $thumbnailPath->storePubliclyAs('products/thumbnails', $thumbnailName, 'do');
                //Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                // وضع اسم الصورة في المصفوفة
                $data_thumbnail['thumbnail'] = $thumbnailName;
            } else {
                return response()->error(__("messages.product.thumbnail_required"), 403);
            }

            /* ---------------------- رفع الصورة على قواعد البيانات --------------------- */
            //بداية المعاملة مع البيانات المرسلة لقاعدة بيانات:
            DB::beginTransaction();
            // عملية التعديل او انشاء الصورة
            $product->update($data_thumbnail);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_upload_thumbnail"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * upload_images => رفع صور العرض
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
    public function upload_galaries($id, ImagesRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->with('galaries')
                ->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وقت رفع الصورة
            $time = time();
            // جلب الصور اذا كان هناك تعديل
            $get_galaries_images =  $product->galaries;
            /* ---------------- معالجة الصور و الملفات و روابط الفيديوهات --------------- */
            // مصفوفة من اجل وضع فيها المعلومات الصور
            $galaries_images = [];

            // شرط اذا كانت هناك صورة مرسلة من قبل المستخدم
            if (count($get_galaries_images) != 0) {
                // شرط اذا كانت هناك صور ارسلت من قبل المستخدم
                if ($request->images) {
                    // عدد الصور التي تم رفعها
                    foreach ($request->file('images') as $key => $value) {
                        $imagelName = "tw-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                        // وضع المعلومات فالمصفوفة
                        $galaries_images[$key] = [
                                'path'      => $imagelName,
                                'full_path' => $value,
                                'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                                'mime_type' => $value->getClientOriginalExtension(),
                            ];
                    }
                    // عملية رفع المفات
                    foreach ($galaries_images as $image) {
                        // رفع الصور
                        $image['full_path']->storePubliclyAs('products/galaries-images', $image['path'], 'do');
                    }
                }
            } else {
                // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة
                if (!$request->images) {
                    return response()->error(__("messages.product.count_galaries"), 403);
                }
                // عدد الصور التي تم رفعها
                foreach ($request->file('images') as $key => $value) {
                    $imagelName = "tw-galary-image-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                    // وضع المعلومات فالمصفوفة
                    $galaries_images[$key] = [
                            'path'      => $imagelName,
                            'full_path' => $value,
                            'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                            'mime_type' => $value->getClientOriginalExtension(),
                        ];
                }
                // شرط اذا كان عدد صور يزيد عند 5 و يقل عن 1
                if (count($galaries_images) > 5 || count($galaries_images) == 0) {
                    return response()->error(__("messages.product.count_galaries"), 403);
                } else {
                    // عملية رفع المفات
                    foreach ($galaries_images as $image) {
                        // رفع الصور
                        $image['full_path']->storePubliclyAs('products/galaries-images', $image['path'], 'do');
                    }
                }
            }


            /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // شرط اذا كانت توجد بيانات الصور في المصفوفة
            if (count($galaries_images) != 0) {
                // انشاء صور جديدة
                $product->galaries()->createMany($galaries_images);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_upload_galaries"), $product->load('galaries'));
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    /**
    * delete_one_galary
    *
    * @param  mixed $id
    * @return void
    */
    public function delete_one_galary($id, Request $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->with('galaries')
                ->first();

            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // galaries is count 1
            if ($product->galaries->count() == 1) {
                return response()->error(__("messages.product.count_galaries"), 403);
            }
            // جلب الصورة من المعرض
            $galary = Galary::whereId($request->id)->where('product_id', $id)->first();
            // تحقق من صورة موجودة
            if (!$galary || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            /* ---------------- معالجة الصور و الملفات و روابط الفيديوهات --------------- */

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                // حذف صورة السابقة
                if (Storage::disk('do')->exists("products/galaries-images/{$galary->path}")) {
                    Storage::disk('do')->delete("products/galaries-images/{$galary->path}");
                }

                // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
                DB::beginTransaction();
                // حذف الصورة المعرض
                $galary->delete();
                // انهاء المعاملة بشكل جيد :
                DB::commit();
                // رسالة نجاح عملية الاضافة:
                return response()->success(__("messages.product.delete_galary"), $galary);
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
            }
            /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */

            // ================================================================
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
