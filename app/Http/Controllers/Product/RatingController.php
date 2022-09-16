<?php

namespace App\Http\Controllers\Product;

use App\Events\Rating as EventsRating;
use App\Events\Reply;
use App\Http\Controllers\Controller;
use App\Http\Requests\Products\RatingStoreRequest;
use App\Http\Requests\ReplyRatingRequest;
use App\Models\Item;
use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class RatingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['abilities:user']);
    }
    public function rate($id, RatingStoreRequest $request)
    {
        //id  جلب العنصر بواسطة
        $item = Item::find($id);
        $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
        $xlocalization = "ar";
        if ($request->headers->has('X-localization'))
            $xlocalization = $request->header('X-localization');
        else {
            $tr->setSource();
            $tr->setTarget('en');
            $tr->translate($request->comment);
            $xlocalization = $tr->getLastDetectedSource();
        }
        $tr->setSource($xlocalization);
        $comment_ar = "";
        $comment_en = "";
        $comment_fr = "";
        $product = Product::withAvg('ratings', 'rating')->whereId($item->number_product)->first();
        // شرط اذا كان العنصر موجود
        if (!$product || !is_numeric($id)) {
            // رسالة خطأ
            return response()->error('هذا العنصر غير موجود', 403);
        }
        // جلب البائع
        $seller = User::find($item->user_id);
        // جلب المستخدم الحالي
        $user_id = Auth::id();

        // قم بجلب تقييم الخدمة من طرف المستخدم الحالي
        $rate = Rating::where('user_id', $user_id)
            ->where('item_id', $item->id)
            ->where('product_id', $product->id)
            ->first();

        if (!$item->is_rating) {
            return response()->error('لا يمكنك التقييم', 403);
        }
        // إذا كان التقييم موجود وغير فارغ يمكن التعديل عليه
        if ($rate) {

            try {
                DB::beginTransaction();

                switch ($xlocalization) {
                    case "ar":
                        $tr->setTarget('en');
                        $comment_en = $tr->translate($request->comment);
                        $tr->setTarget('fr');
                        $comment_fr = $tr->translate($request->comment);
                        $comment_ar = $request->comment;
                        break;
                    case 'en':
                        $tr->setTarget('ar');
                        $comment_ar = $tr->translate($request->comment);
                        $tr->setTarget('fr');
                        $comment_fr = $tr->translate($request->comment);
                        $comment_en = $request->comment;
                        break;
                    case 'fr':
                        $tr->setTarget('en');
                        $comment_en = $tr->translate($request->comment);
                        $tr->setTarget('ar');
                        $comment_ar = $tr->translate($request->comment);
                        $comment_fr = $request->comment;
                        break;
                }
                // قم بإنشاء تقييم جديد
                $rate->update([
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'comment_ar' => $comment_ar,
                    'comment_fr' => $comment_fr,
                    'comment_en' => $comment_en,
                    'status' => Rating::RATING_SUSPEND
                ]);
                // ثم جلب الخدمة من جديد لتعديل الحقلين : عدد التقييمات ومعدل التقييمات
                $product = $this->getRatedProduct($rate->id);
                //$product->increment('ratings_count');
                $product->ratings_avg = $product->ratings_avg_rating;
                // حفظ التعديلات الجديدة على الخدمة
                $product->save();

                $item->is_rating = false;
                $item->save();
                event(new EventsRating(
                    $seller,
                    $product->slug,
                    $product->title,
                    $rate->id,
                    $product->title_ar,
                    $product->title_en,
                    $product->title_fr,
                ));
                DB::commit();
                // إرسال رسالة النجاح
                return response()->success('لقد تمّ التعديل التقييم بنجاح', $rate);
            } catch (Exception $ex) {
                // في حالة الخطأ يتم التراجع عن أي تغيير حدث في قاعدة البيانات
                DB::rollback();
                //eturn $ex;
                // ثم إرسال رسالة الخطأ
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        } else {
            // في حالة عدم وجود تقييم لهذه الخدمة من طرف المستخدم الحالي
            try {
                DB::beginTransaction();
                switch ($xlocalization) {
                    case "ar":
                        $tr->setTarget('en');
                        $comment_en = $tr->translate($request->comment);
                        $tr->setTarget('fr');
                        $comment_fr = $tr->translate($request->comment);
                        $comment_ar = $request->comment;
                        break;
                    case 'en':
                        $tr->setTarget('ar');
                        $comment_ar = $tr->translate($request->comment);
                        $tr->setTarget('fr');
                        $comment_fr = $tr->translate($request->comment);
                        $comment_en = $request->comment;
                        break;
                    case 'fr':
                        $tr->setTarget('en');
                        $comment_en = $tr->translate($request->comment);
                        $tr->setTarget('ar');
                        $comment_ar = $tr->translate($request->comment);
                        $comment_fr = $request->comment;
                        break;
                }
                // قم بإنشاء تقييم جديد
                $rating = Rating::create([
                    'user_id' => $user_id,
                    'product_id' => $product->id,
                    'item_id' => $item->id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'comment_ar' => $comment_ar,
                    'comment_fr' => $comment_fr,
                    'comment_en' => $comment_en,
                    'status' => Rating::RATING_SUSPEND
                ]);
                // ثم جلب الخدمة من جديد لتعديل الحقلين : عدد التقييمات ومعدل التقييمات
                $product = $this->getRatedProduct($rating->id);
                $product->increment('ratings_count');
                $product->ratings_avg = $product->ratings_avg_rating;
                // حفظ التعديلات الجديدة على الخدمة
                $product->save();

                $item->is_rating = false;
                $item->save();
                event(new EventsRating(
                    $seller,
                    $product->slug,
                    $product->title,
                    $product->title_ar,
                    $product->title_en,
                    $product->title_fr,
                    $rating->id
                ));
                DB::commit();
                // إرسال رسالة النجاح
                return response()->success('لقد تمّ إضافة  التقييم بنجاح', $rating);
            } catch (Exception $ex) {
                // في حالة الخطأ يتم التراجع عن أي تغيير حدث في قاعدة البيانات
                DB::rollback();
                //eturn $ex;
                // ثم إرسال رسالة الخطأ
                return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
            }
        }
    }

    public function reply($id, ReplyRatingRequest $request)
    {

        // قم بجلب تقييم الخدمة من طرف المستخدم الحالي
        $rate = Rating::find($id);
        // إذا كان التقييم موجود وغير فارغ يمكن التعديل عليه
        if (!$rate) {
            return response()->error('لا يوجد تقييم', 403);
        } else {
            // في حالة عدم وجود تقييم لهذه الخدمة من طرف المستخدم الحالي
            try {
                $product = Product::withAvg('ratings', 'rating')->whereId($rate->product_id)->first();
                $buyer = User::find($rate->user_id);
                DB::beginTransaction();

                // قم بإنشاء تقييم جديد
                $rate->reply = $request->reply;
                $rate->save();

                event(new Reply(
                    $buyer,
                    $product->id,
                    $product->title,
                    $product->title_ar,
                    $product->title_en,
                    $product->title_fr,
                    $rate->id
                ));

                DB::commit();
                // إرسال رسالة النجاح
                return response()->success('تمّ إضافة ردك بنجاح', $rate);
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
        return Product::select('id', 'slug')->whereHas('ratings', function ($q) use ($rating_id) {
            $q->where('id', $rating_id);
        })
            ->withAvg('ratings', 'rating')
            ->first();
    }
}
