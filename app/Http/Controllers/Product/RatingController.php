<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\RatingStoreRequest;
use App\Models\Product;
use App\Models\Rating;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function rate($id, RatingStoreRequest $request)
    {
        //id  جلب العنصر بواسطة
        $product = Product::withAvg('ratings', 'rating')->find($id);
        // جلب المستخدم الحالي
        $user_id = Auth::id();
        // شرط اذا كان العنصر موجود
        if (!$product || !is_numeric($id))
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        // قم بجلب تقييم الخدمة من طرف المستخدم الحالي 
        $rate = Rating::where('user_id', $user_id)->where('product_id', $product->id)->first();

        // إذا كان التقييم موجود وغير فارغ يمكن التعديل عليه
        if (!empty($rate)) {

            try {
                // إعطاء قيمة جديدة للتقييم والتعليق
                $rate->rating = $request->rating;
                $rate->comment = $request->comment;
                DB::beginTransaction();
                // تحديث المعلومات
                $rate->update();
                // جلب الخدمة من جديد لتحديث حقل متوسط التقييمات
                $product = $this->getRatedProduct($rate->id);
                // إعطاء قيمة جديدة لمتوسط التقييمات
                $product->ratings_avg = $product->ratings_avg_rating;
                // حفظ القيمة الجديدة لمعدل التقييمات
                $product->save();
                DB::commit();
                // إرسال رسالة تفيد بنجاح الأمر
                return response()->success('لقد تمّ التعديل على التقييم بنجاح', $rate);
            } catch (Exception $ex) {
                // وإلا قم بالتراجع عن التغيير في حالة حدوث خطأ
                DB::rollback();
                //return $ex;
                // وإرسال رسالة تفيد بوجود خطأ غير متوقع
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        } else {
            // في حالة عدم وجود تقييم لهذه الخدمة من طرف المستخدم الحالي
            try {
                DB::beginTransaction();
                // قم بإنشاء تقييم جديد
                $rating = Rating::create([
                    'user_id' => $user_id,
                    'product_id' => $product->id,
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ]);
                // ثم جلب الخدمة من جديد لتعديل الحقلين : عدد التقييمات ومعدل التقييمات
                $product = $this->getRatedProduct($rating->id);
                $product->increment('ratings_count');
                $product->ratings_avg = $product->ratings_avg_rating;
                // حفظ التعديلات الجديدة على الخدمة
                $product->save();

                DB::commit();
                // إرسال رسالة النجاح
                return response()->success('لقد تمّ إضافة  التقييم بنجاح', $rating);
            } catch (Exception $ex) {
                // في حالة الخطأ يتم التراجع عن أي تغيير حدث في قاعدة البيانات
                DB::rollback();
                //return $ex;
                // ثم إرسال رسالة الخطأ
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        }
    }

    /**
     * دالة لجلب الخدمة التي تم تقييمها 
     *
     * @param  $rating_id
     */
    public function getRatedProduct($rating_id)
    {
        return Product::select('id')->whereHas('ratings', function ($q) use ($rating_id) {
            $q->where('id', $rating_id);
        })
            ->withAvg('ratings', 'rating')
            ->first();
    }
}
