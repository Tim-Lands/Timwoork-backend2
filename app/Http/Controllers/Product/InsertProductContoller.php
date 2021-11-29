<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\{
    ProductStepFourRequest,
    ProductStepOneRequest,
    ProductStepThreeRequest,
    ProductStepTwoRequest
};
use App\Models\{Category, Product, Shortener, Tag};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InsertProductContoller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * show => عرض الخدمة الواحدة
     *
     * @param  string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        // slug جلب الخدمة بواسطة 
        $product = Product::selection()
            ->whereSlug($slug)
            ->orWhere('id', $slug)
            ->first();
        if (!$product)
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // اظهار العناصر
        return response()->success('عرض خدمة', $product);
    }

    /**
     * create => دالة جلب البيانات و انشاء معرف جديد
     *
     * @return void
     */
    public function store(): JsonResponse
    {
        try {
            // ============= انشاء المعرف للخدمة ================:

            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء معرف جديد للخدمة
            $product = Product::create(['profile_seller_id' => Auth::user()->id]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();

            // اظهار العناصر
            return response()->success('عرض كل تصنيفات الرئيسية و الفرعيىة و الوسوم من اجل انشاء خدمة ', Product::selection()->where('id', $product->id)->first());
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * storeStepOne => دالة انشاء المرحلة الاولى من الخدمة
     * @package
     * @param  ProductStepOneRequest $request
     * @return object
     */
    public function storeStepOne($id, ProductStepOneRequest $request): JsonResponse
    {
        try {

            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);

            // جلب التصنيف الفرعي
            $subcategory = Category::child()->where('id', $request->subcategory)->exists();
            // التحقق اذا كان موجود ام لا
            if (!$subcategory)
                return response()->error('التصنيف الفرعي لا يوجد', 403);
            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            $data = [
                'title'             => $request->title,
                'slug'              => Str::slug($request->title),
                'category_id'       =>  (int)$request->subcategory,
                'profile_seller_id' => /*Auth::user()->profile->profile_seller->id*/ 2,
                'current_step'      => Product::PRODUCT_STEP_ONE
            ];
            // ============= انشاء المرحلة الاولى في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الاولى
            $product->update($data);
            // اضافة الكلمات المفتاحية الكلمات المفتاحية او الوسوم
            $product->product_tag()->syncWithoutDetaching(collect($request->tags));
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء المرحلة الاولى بنجاح', $product);
            // =======================================================
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * storeStepTwo => دالة انشاء المرحلة الثانية من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepTwoRequest $request
     * @return object
     */
    public function storeStepTwo(mixed $id, ProductStepTwoRequest $request): JsonResponse
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثانية
            $data = [
                'price'           => $request->price,
                'duration'        => (int)$request->duration,
                'current_step'    => Product::PRODUCT_STEP_TWO,
            ];
            // انشاء مصفوفة جديدة من اجل عملية اضافة تطويرات
            $developments = [];
            // شرط اذا كانت هناك توجد تطورات
            if ($request->only('developments') != null) {
                // جلب المرسلات من العميل و وضعهم فالمصفوفة الجديدة
                foreach ($request->only('developments') as $key => $value) {
                    $developments[] = $value;
                }
                $developments = $developments[0];
            }
            // return $product->develpments;
            // ============= انشاء المرحلة الثانية في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثانية
            $product->update($data);
            // شرط اذا كانت هناط تطويرات من قبل
            if ($product->develpments)
                // حدف كل التطويرات
                $product->developments()->delete();

            // اضافة تطويرات جديدة 
            $product->developments()->createMany($developments);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء المرحلة الثانية بنجاح', $product);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * storeStepThree => دالة انشاء المرحلة الثالثة من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepThreeRequest $request
     * @return JsonResponse
     */
    public function storeStepThree(mixed $id, ProductStepThreeRequest $request): JsonResponse
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // وضع البيانات في مصفوفة من اجل اضافة فالمرحلة الثالثة
            $data = [
                'buyer_instruct'  => $request->buyer_instruct,
                'content'         => $request->content,
                'current_step'    => Product::PRODUCT_STEP_THREE,
            ];
            // ============= انشاء المرحلة الثالثة في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء المرحلة الثالثة بنجاح', $product);
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    /**
     * storeStepFour => دالة انشاء المرحلة الرابعة من الخدمة
     *
     * @param  mixed $id
     * @param  ProductStepFourRequest $request
     * @return JsonResponse
     */
    public function storeStepFour(mixed $id, ProductStepFourRequest $request): JsonResponse
    {
        try {

            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // ============================ معالجة الصورة الامامية ==========================================
            $time = time();
            // وضع معلومات في مصفوفة من اجل عملية الانشاء
            $data_product = ['current_step'  => Product::PRODUCT_STEP_FOUR];
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
                    $data_product['thumbnail'] = $thumbnailName;
                }
            } else {
                // شرط اذا لم يرسل المستخدم صورة الامامية
                if (!$request->has('thumbnail')) {
                    return response()->error('يجب عليك رفع الصورة الامامية', 403);
                }
                // جلب الصورة من المرسلات
                $thumbnailPath = $request->file('thumbnail');
                // وضع اسم جديد للصورة
                $thumbnailName = "tw-thumbnail-{$product->slug}-{$id}-{$time}.{$thumbnailPath->getClientOriginalExtension()}";
                // رفع الصورة الامامية للخدمة
                Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
                // وضع اسم الصورة في المصفوفة
                $data_product['thumbnail'] = $thumbnailName;
            }

            // جلب الصور اذا كان هناك تعديل
            $get_galaries_images =  $product->whereId($id)->with(['galaries' => function ($q) {
                $q->select('id', 'path', 'product_id', 'type_file')->where('type_file', 'image')->get();
            }])->first()['galaries'];
            // جلب الملف اذا كان هناك تعديل
            $get_galaries_file =  $product->whereId($id)->with(['galaries' => function ($q) {
                $q->select('id', 'path', 'full_path', 'product_id', 'type_file')->where('type_file', 'file')->get();
            }])->first()['galaries'];

            // جلب رابط الفيديو
            $get_galaries_url_video =  $product->whereId($id)->with(['galaries' => function ($q) use ($request) {
                $q->select('id', 'url_video', 'product_id')->where('url_video', $request->url_video)->get();
            }])->first()['galaries'];
            // ==========================================================================================
            // ========================== معالجة الصور و الملفات و روابط الفيديوهات ===============================
            // مصفوفة من اجل وضع فيها المعلومات الصور    
            $galaries_images = [];
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
                            'type_file' => 'image',
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
                if (!$request->has('images'))
                    return response()->error('يجب ان يكون عدد الصور المرفوعة لا تزيد عن  5 و لا تقل عن 1', 403);
                // عدد الصور التي تم رفعها
                foreach ($request->file('images') as $key => $value) {
                    $imagelName = "tw-galary-image-{$product->slug}-{$key}-{$time}.{$value->getClientOriginalExtension()}";
                    // وضع المعلومات فالمصفوفة
                    $galaries_images[$key] = [
                        'path'      => $imagelName,
                        'full_path' => $value,
                        'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                        'mime_type' => $value->getClientOriginalExtension(),
                        'type_file' => 'image',
                    ];
                }
                // شرط اذا كان عدد صور يزيد عند 5 و يقل عن 1
                if (count($galaries_images) > 5 || count($galaries_images) == 0)
                    return response()->error('يجب ان يكون عدد الصور المرفوعة لا تزيد عن  5 و لا تقل عن 1', 403);
                else {
                    // عملية رفع المفات
                    foreach ($galaries_images as $image) {
                        // رفع الصور
                        Storage::putFileAs('products/galaries-images', $image['full_path'], $image['path']);
                    }
                }
            }


            // pdf مصفوفة من اجل وضع فيها المعلومات الملف    
            $galary_file = [];
            // شرط في حالة ما تم ارسال ملف جديد
            if ($request->has('file')) {
                if (count($get_galaries_file) != 0) {
                    // حذف الملف السابق
                    Storage::delete("products/galaries-file/{$get_galaries_file['path']}");
                    // وضع اسم للملف
                    $filelName = "tw-galary-file-{$product->slug}-{$id}-{$time}.{$request->file('file')->getClientOriginalExtension()}";
                    // انشاء مصفوفة جديدة من اجل حفظ المعلومات في قواعد البيانات
                    $galary_file = [
                        'path'       => $filelName,
                        'full_path'  => $request->file('file'),
                        'size'       => number_format($request->file('file')->getSize() / 1048576, 3) . ' MB',
                        'mime_type'  => $request->file('file')->getClientOriginalExtension(),
                        'type_file'  => 'file',
                        'product_id' => $product->id,
                        "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                    ];
                    // رفع الملف
                    Storage::putFileAs('products/galaries-file', $request->file('file'), $filelName);
                } else {
                    // وضع اسم للملف
                    $filelName = "tw-galary-file-{$product->slug}-{$id}-{$time}.{$request->file('file')->getClientOriginalExtension()}";
                    // انشاء مصفوفة جديدة من اجل حفظ المعلومات في قواعد البيانات
                    $galary_file = [
                        'path'       => $filelName,
                        'full_path'  => $request->file('file'),
                        'size'       => number_format($request->file('file')->getSize() / 1048576, 3) . ' MB',
                        'mime_type'  => $request->file('file')->getClientOriginalExtension(),
                        'type_file'  => 'file',
                        'product_id' => $product->id,
                        "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                    ];
                    // رفع الملف
                    Storage::putFileAs('products/galaries-file', $request->file('file'), $filelName);
                }
            }

            // ====================== انشاء المرحلة الثالثة في الخدمة =====================================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data_product);

            // شرط اذا كانت توجد بيانات الصور في المصفوفة
            if ($galaries_images) {
                // شرط اذا كانت توجد بيانات الصور من قبل
                if ($get_galaries_images) {
                    // حذف كل الصور القديمة
                    $product->galaries()->where('type_file', 'image')->delete();
                }
                // انشاء صور جديدة
                $product->galaries()->createMany($galaries_images);
            }
            // شرط اذا كانت توجد بيانات الملف في المصفوفة
            if ($galary_file) {
                // شرط اذا كانت توجد بيانات الملف من قبل
                if (count($get_galaries_file) != 0) {
                    // عملية التعديل على الملف
                    $product->galaries()->where('type_file', 'file')->update($galary_file);
                } else {
                    // انشاء ملف جديد
                    $product->galaries()->insert($galary_file);
                }
            }
            // شرط اذا كانت هناك ارسال رابط في فيديو من قبل المستخدم
            if ($request->has('url_video')) {
                // شرط اذا كانت توجد بيانات رابط الفيديو من قبل
                if (count($get_galaries_url_video) != 0) {
                    // عملية التعديل على رابط الفيديو
                    $product->galaries()->where('url_video', $request->url_video)->update([
                        'url_video' => $request->url_video
                    ]);
                } else {
                    // انشاء رابط فيديو جديد
                    $product->galaries()->insert([
                        'url_video' => $request->url_video,
                        'product_id' => $product->id,
                        "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                        "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                    ]);
                }
            }
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء المرحلة الرابعة بنجاح', $product);
            // ========================================================

        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
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
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id)) {
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
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
                'is_draft'      => 1,
                'current_step'  => Product::PRODUCT_STEP_FIVE,
                'is_completed'  => Product::PRODUCT_IS_COMPLETED,
            ];
            // ============= انشاء المرحلة الاخيرة في الخدمة و نشرها ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data);
            // شرط هل يوجد رابط مختصر من قبل
            if (!$shorterner)
                $product->shortener()->create($data_shortener);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // ================================================================
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انهاء المراحل و انشاء الخدمة بنجاح', $product);
        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
}
