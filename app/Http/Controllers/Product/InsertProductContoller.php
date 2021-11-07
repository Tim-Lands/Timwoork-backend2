<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\{
    ProductStepFourRequest,
    ProductStepOneRequest,
    ProductStepThreeRequest,
    ProductStepTwoRequest
};
use App\Models\{Category, Product, Tag};
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InsertProductContoller extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth:sanctum');
    }

    public function create()
    {
        $data = [
            'categories'    => Category::selection()->parent()->pluck('name_ar', 'id'),
            'tags'          => Tag::selection()->pluck('name_ar', 'id')
        ];
        // اظهار العناصر
        return response()->success('عرض كل تصنيفات الرئيسية و الفرعيىة و الوسوم من اجل انشاء خدمة ', $data);
    }

    /**
     * storeStepOne => دالة انشاء المرحلة الاولى من الخدمة
     *
     * @param  ProductStepOneRequest $request
     * @return object
     */
    public function storeStepOne(ProductStepOneRequest $request): JsonResponse
    {
        try {

            // جلب التصنيف الفرعي
            $subcategory = Category::child()->where('id', $request->subcategory)->exists();
            // التحقق اذا كان موجود ام لا
            if (!$subcategory)
                return response()->error('التصنيف الفرعي لا يوجد', 403);
            // انشاء مصفوفة و وضع فيها بيانات المرحلة الاولى
            $data = [
                'title'             => $request->title,
                'slug'              => Str::slug($request->title),
                'content'           => $request->content,
                'category_id'       =>  (int)$request->subcategory,
                'profile_seller_id' => 2,
                'current_step'      => Product::PRODUCT_STEP_ONE
            ];
            // ============= انشاء المرحلة الاولى في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الاولى
            $product = Product::create($data);
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
            return $ex;
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
                'duration'        => $request->duration,
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
            // ============= انشاء المرحلة الثانية في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثانية
            $product->update($data);
            //شرط اذا كانت هناك تطورات
            if ($developments)
                // اضافة تطويرات 
                $product->develpments()->createMany($developments);
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
     * storeStepFour
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
            // ===================== معالجة الصورة الامامية =================================
            // جلب الصورة من المرسلات
            $thumbnailPath = $request->file('thumbnail');
            // وضع اسم جديد للصورة
            $thumbnailName = 'tw-thumbnail' . '-' . $product->slug . '-' . $id . '.' . $thumbnailPath->getClientOriginalExtension();
            // رفع الصورة الامامية للخدمة
            Storage::putFileAs('products/thumbnails', $request->file('thumbnail'), $thumbnailName);
            // وضع معلومات في مصفوفة من اجل عملية الانشاء
            $data_product = [
                'thumbnail'     => $thumbnailName,
                'current_step'  => Product::PRODUCT_STEP_FOUR,
            ];
            // ========================================================================
            // ================== معالجة الصور و الملفات و روابط الفيديوهات =====================
            // مصفوفة من اجل وضع فيها المعلومات الصور    
            $galaries_images = [];
            // عدد الصور التي تم رفعها
            foreach ($request->file('images') as $key => $value) {
                $imagelName = 'tw-galary-image' . '-' . $product->slug . '-' . $key . '.' . $value->getClientOriginalExtension();
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
                foreach ($galaries_images as $image) {
                    // رفع الصور
                    Storage::putFileAs('products/galaries-images', $image['full_path'], $image['path']);
                }
            }
            // pdf مصفوفة من اجل وضع فيها المعلومات الملف    
            $galary_file = [];
            // pdf شرط في حالة ما اذا وجد الملف
            if ($request->has('file')) {
                $filelName = 'tw-galary-file' . '-' . $product->slug . '-' . $key . '.' . $request->file('file')->getClientOriginalExtension();
                $galary_file = [
                    'path'      => $filelName,
                    'full_path' => $request->file('file'),
                    'size'      => number_format($value->getSize() / 1048576, 3) . ' MB',
                    'mime_type' => $request->file('file')->getClientOriginalExtension(),
                    'type_file' => 'file',
                    'product_id' => $product->id,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                ];
                // رفع الملف
                Storage::putFileAs('products/galaries-file', $value, $filelName);
            }

            // ============= انشاء المرحلة الثالثة في الخدمة ================:
            // بداية المعاملة مع البيانات المرسلة لقاعدة بيانات :
            DB::beginTransaction();
            // عملية انشاء المرحلة الثالثة
            $product->update($data_product);
            if ($galaries_images)
                $product->galaries()->createMany($galaries_images);
            if ($galary_file)
                $product->galaries()->insert($galary_file);
            if ($request->has('url_video'))
                $product->galaries()->insert([
                    'url_video' => $request->url_video,
                    'product_id' => $product->id,
                    "created_at" =>  \Carbon\Carbon::now(), # new \Datetime()
                    "updated_at" => \Carbon\Carbon::now(),  # new \Datetime()
                ]);
            // انهاء المعاملة بشكل جيد :
            DB::commit();
            // رسالة نجاح عملية الاضافة:
            return response()->success('تم انشاء المرحلة الرابعة بنجاح', $product);
            // ========================================================

        } catch (Exception $ex) {
            return $ex;
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }

    public function storeStepFive($id)
    {
        try {
            //id  جلب العنصر بواسطة
            $product = Product::find($id);
            // شرط اذا كان العنصر موجود
            if (!$product || !is_numeric($id))
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            // وضع معلومات في مصفوفة من اجل عملية الانشاء
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
