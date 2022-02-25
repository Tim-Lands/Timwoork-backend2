<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\ImagesRequest;
use App\Http\Requests\Products\ProductStepFourRequest;
use App\Http\Requests\Products\ProductStepOneRequest;
use App\Http\Requests\Products\ProductStepThreeRequest;
use App\Http\Requests\Products\ProductStepTwoRequest;
use App\Http\Requests\Products\ThumbnailRequest;
use App\Models\Category;
use App\Models\File;
use App\Models\Product;
use App\Models\Shortener;
use App\Models\Tag;
use App\Models\Video;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InsertProductContoller extends Controller
{
    /**
     * create => دالة جلب البيانات و انشاء معرف جديد
     *
     * @return void
     */
    public function store()
    {
        try {
            //جلب عدد خدمات
            $count_products_seller =  Auth::user()->profile->profile_seller->products->count();
            // جلب عدد المطلبوب من انشاء الخدمة من المستوى
            $number_of_products_seller = Auth::user()->profile->profile_seller->level->products_number_max;
            // شرط اضافة خدمة
            if ($count_products_seller >= $number_of_products_seller) {
                return response()->error(__("messages.product.number_of_products_seller"), 422);
            }

            // ============= انشاء المعرف للخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء معرف جديد للخدمة
            $product = Product::create([
                'profile_seller_id' => Auth::user()->profile->profile_seller->id,
                'is_draft'          => Product::PRODUCT_IS_DRAFT
            ]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // اظهار العناصر
            return response()->success(__("messages.oprations.get_all_data"), Product::selection()->where('id', $product->id)->first());
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * storeStepOne => دالة انشاء المرحلة الاولى من الخدمة
     * @package
     * @param  ProductStepOneRequest $request
     * @return object
     */
    public function storeStepOne($id, ProductStepOneRequest $request)
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
                'slug'              => slug_with_arabic($request->title),
                'category_id'       =>  (int)$request->subcategory,
            ];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_ONE) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_ONE;
            }
            // جلب الوسوم من المستخدم
            $tag_values = array_map(function ($key) {
                return $key["value"] ;
            }, $request->tags);
            // حلب الوسوم الموجودة داخل القواعد البيانات
            $tags = Tag::whereIn("name", $tag_values)->get();

            // مصفوفة فارغة
            $tags_total = [];
            // جلب الاسماء الوسوم فقط
            $get_name_tags = array_map(function ($key) {
                return $key["name"] ;
            }, $tags->toArray());
            // جلب معرفات الوسوم و وضعهم في مصفوفة
            $tags_total = array_map(function ($key) {
                return $key["id"] ;
            }, $tags->toArray());
            // فلترة اسماء الوسوم من التكرار المتواجدة في قواعد البيانات
            $filter_tags = array_unique($get_name_tags);
            // جلب الاسماء الجديدة الغير موجودة في قواعد البيانات
            $new_tags = array_values(array_diff($tag_values, $filter_tags));
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
                    $tag = Tag::create(['name' => $tag]);
                    // وضع معرف الوسم في المصفوفة
                    $tags_total[] = $tag->id;
                }
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->syncWithoutDetaching($tags_total);
            } else {
                // اضافة وسوم التابع للخدمة
                $product->product_tag()->syncWithoutDetaching($tags_total);
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
     * storeStepTwo => دالة انشاء المرحلة الثانية من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepTwoRequest $request
     * @return object
     */
    public function storeStepTwo(mixed $id, ProductStepTwoRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // جلب عدد المطلبوب من انشاء التطويرات من المستوى
            $number_developments_max = Auth::user()->profile->profile_seller->level->number_developments_max;
            // جلب عدد المطلبوب من السعر التطويرات من المستوى
            $price_development_max = Auth::user()->profile->profile_seller->level->price_development_max;
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
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_TWO) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_TWO;
            }
            // انشاء مصفوفة جديدة من اجل عملية اضافة تطويرات
            (object)$developments = [];
            // شرط اذا كانت هناك توجد تطورات
            if ($request->only('developments') != null) {
                if (count($request->developments) >= $number_developments_max) {
                    return response()->error(__("messages.product.number_developments_max"), 422);
                }
                // جلب المرسلات من العميل و وضعهم فالمصفوفة الجديدة
                foreach ($request->only('developments')['developments'] as $key => $value) {
                    $developments[] = $value;
                    // اذا كان السعر اكبر
                    if ($value['price'] >= $price_development_max) {
                        return response()->error(__("messages.product.price_development_max"), 422);
                    }
                }
            }
            // =============== انشاء المرحلة الثانية في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثانية
            $product->update($data);
            // شرط اذا كانت هناط تطويرات من قبل
            if ($product->develpments) {
                // حدف كل التطويرات
                $product->developments()->delete();
            }

            // اضافة تطويرات جديدة
            $product->developments()->createMany($developments);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_two"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * storeStepThree => دالة انشاء المرحلة الثالثة من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepThreeRequest $request
     * @return JsonResponse
     */
    public function storeStepThree(mixed $id, ProductStepThreeRequest $request)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();     // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثالثة
            $data = [
                'buyer_instruct'  => $request->buyer_instruct,
                'content'         => $request->content,
            ];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_THREE) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_THREE;
            }
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
     * storeStepFour => دالة انشاء المرحلة الرابعة من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepFourRequest $request
     * @return JsonResponse
     */
    public function storeStepFour(mixed $id, ProductStepFourRequest $request)
    {
        try {

            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
                ->with(['galaries','video'])
                ->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            if (count($product->galaries) == 0 && $product->thumbnail) {
                // رسالة خطأ
                return response()->error(__("messages.errors.upload_images"), 422);
            }

            $data = [];
            // دراسة حالة المرحلة
            if ($product->is_completed == 1 || $product->current_step > Product::PRODUCT_STEP_FOUR) {
                $data['current_step'] = $product->current_step;
            } else {
                $data['current_step'] = Product::PRODUCT_STEP_FOUR;
            }
            // جلب رابط الفيديو
            $get_galaries_url_video =  $product->video;

            // ====================== انشاء المرحلة الرابعة في الخدمة =====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // تعديل على الخدمة
            $product->update($data);
            // شرط اذا كانت هناك ارسال رابط في فيديو من قبل المستخدم
            if ($request->has('url_video')) {
                // شرط اذا كانت توجد بيانات رابط الفيديو من قبل
                if ($get_galaries_url_video) {
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
            return response()->success(__("messages.product.success_step_four"), $product);
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
     * storeStepFive => => دالة انشاء المرحلة الخامسة من الخدمة
     *
     * @param  mixed $id
     * @return JsonResponse
     */
    public function storeStepFive($id): JsonResponse
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::whereId($id)
            ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
                exit();
            }
            // شرط هل يوجد رابط مختصر من قبل
            $shorterner = Shortener::whereProductId($id)->exists();
            // شرط اذا كان لا يوجد رابط مختصر
            if (!$shorterner) {
                // وضع معلومات في مصفوفة من اجل عملية الانشاء رابط مختصر
                $data_shortener = [
                    'code'  => Str::random(7),
                    'url'  => "http://timwoork.test/api/product/{$product['slug']}"
                ];
            }
            //  وضع معلومات في مصفوفة من اجل عملية الانشاء المرحلة الخامسة
            $data = [
                'is_draft'      => Product::PRODUCT_IS_NOT_DRAFT,
                'current_step'  => Product::PRODUCT_STEP_FIVE,
                'is_completed'  => Product::PRODUCT_IS_COMPLETED,
                'is_active'     => Product::PRODUCT_ACTIVE,
            ];
            // ============= انشاء المرحلة الاخيرة في الخدمة و نشرها ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // شرط هل يوجد رابط مختصر من قبل
            if (!$shorterner) {
                $product->shortener()->create($data_shortener);
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success(__("messages.product.success_step_final"), $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
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
            $product = Product::whereId($id)
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)->first();
            ;
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع مصفوفة من اجل عملية التعديل
            $data_thumbnail = [];
            /* ------------------------- معالجة الصورة الامامية ------------------------- */

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                $time = time();
                // شرط في حالة ما اذا كانت الصورة مرسلة من المستخدم
                if ($product->thumbnail) {
                    // شرط اذا قام المستخدم بأرسال صورة الامامية
                    if ($request->has('thumbnail')) {
                        // حذف صورة السابقة
                        Storage::delete("products/thumbnails/{$product->thumbnail}");
                        // جلب الصورة من المرسلات
                        $thumbnailPath = $request->file('thumbnail');
                        // وضع اسم جديد للصورة
                        $thumbnailName = "tw-thumbnail-{$product->slug}-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                        // رفع الصورة الامامية للخدمة
                        Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                        // وضع اسم الصورة في المصفوفة
                        $data_thumbnail['thumbnail'] = $thumbnailName;
                    }
                    return;
                } elseif ($request->has('thumbnail')) {
                    // جلب الصورة من المرسلات
                    $thumbnailPath = $request->file('thumbnail');
                    // وضع اسم جديد للصورة
                    $thumbnailName = "tw-thumbnail-{$product->slug}-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                    // رفع الصورة الامامية للخدمة
                    Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                    // وضع اسم الصورة في المصفوفة
                    $data_thumbnail['thumbnail'] = $thumbnailName;
                } else {
                    return response()->error(__("messages.product.thumbnail_required"), 403);
                }
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
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
                ->where('profile_seller_id', Auth::user()->profile->profile_seller->id)
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

            if ($product->current_step >= Product::PRODUCT_STEP_THREE) {
                // شرط اذا كانت هناك صورة مرسلة من قبل المستخدم
                if (count($get_galaries_images) != 0) {
                    // شرط اذا كانت هناك صور ارسلت من قبل المستخدم
                    if ($request->has('images')) {
                        foreach ($get_galaries_images as $image) {
                            Storage::has("products/galaries-images/{$image['path']}") ? Storage::delete("products/galaries-images/{$image['path']}") : '';
                        }
                        // عدد الصور التي تم رفعها
                        foreach ($request->file('images') as $key => $value) {
                            $imagelName = "tw-galary-image-{$product->slug}-{$key}-{$time}.{$value->getClientOriginalExtension()}";
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
                            Storage::putFileAs('products/galaries-images', $image['full_path'], $image['path']);
                        }
                    }
                } else {
                    // شرط اذا لم يجد الصور التي يرسلهم المستخدم في حالة الانشاء لاول مرة
                    if (!$request->has('images')) {
                        return response()->error(__("messages.product.count_galaries"), 403);
                    }
                    // عدد الصور التي تم رفعها
                    foreach ($request->file('images') as $key => $value) {
                        $imagelName = "tw-galary-image-{$product->slug}-{$key}-{$time}.{$value->getClientOriginalExtension()}";
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
                            Storage::putFileAs('products/galaries-images', $image['full_path'], $image['path']);
                        }
                    }
                }
            } else {
                return response()->error(__("messages.oprations.nothing_this_operation"), 403);
            }

            /* -------------------- رفع الصور العرض في قواعد البيانات ------------------- */
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // شرط اذا كانت توجد بيانات الصور في المصفوفة
            if (count($galaries_images) != 0) {
                // شرط اذا كانت توجد بيانات الصور من قبل
                if ($get_galaries_images) {
                    // حذف كل الصور القديمة
                    $product->galaries()->delete();
                }
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
}
