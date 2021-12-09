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
    public function rate($id, RatingStoreRequest $request)
    {
        //id  جلب العنصر بواسطة
        $product = Product::find($id);
        // شرط اذا كان العنصر موجود
        if (!$product || !is_numeric($id))
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);

        $user_id = 3;
        $rate = Rating::where('user_id', $user_id)->where('product_id', $product->id)->first();
        if (!empty($rate)) {
            $rate->rating = $request->rating;
            $rate->comment = $request->comment;
            try {
                DB::beginTransaction();
                $rate->update();
                DB::commit();
                return response()->success('لقد تمّ التعديل على التقييم بنجاح', $rate);
            } catch (Exception $ex) {
                DB::rollback();
                return $ex;
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        } else {
            try {
                DB::beginTransaction();
                $rating = Rating::create([
                    'user_id' => $user_id,
                    'product_id' => $product->id,
                    'rating' => $request->rating,
                    'comment' => $request->comment
                ]);
                DB::commit();
                return response()->success('لقد تمّ إضافة  التقييم بنجاح', $rating);
            } catch (Exception $ex) {
                DB::rollback();
                return $ex;
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        }
    }
}
