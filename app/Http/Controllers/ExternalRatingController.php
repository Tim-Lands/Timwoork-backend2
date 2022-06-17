<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExternalRatingRequest;
use App\Http\Requests\UpdateExternalRatingRequest;
use App\Models\ExternalRating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExternalRatingController extends Controller
{

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum','abilities:user']);
    }

    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ?? 10;
        $type = $request->query('type');
        $rattings = ExternalRating::where('status', $type)
            ->with('product.profileSeller.profile')
            ->paginate($paginate);
        return response()->success("لقد تمّ جلب البيانات بنجاح", $rattings);
    }

    public function show($id)
    {
        $external_rating = ExternalRating::find($id);
        return response()->success("لقد تمّ جلب البيانات بنجاح", $external_rating);
    }

    public function accept($id, Request $request)
    {
        $external_rating = ExternalRating::whereId($id)->first();
        if ($external_rating->status == 1) {
            return response()->error("لقد تم قبول الطلب سابقا", 403);
        }
        if ($external_rating->status == 2) {
            return response()->error("لقد تم رفض الطلب سابقا", 403);
        }
        try {
            DB::beginTransaction();
            $external_rating->status = 1;
            $external_rating->save();

            $external_rating->product->external_rating = $request->external_rating;
            $external_rating->product->external_ratings_count = $request->external_rating_count;
            $external_rating->product->save();
            DB::commit();
            return response()->success("لقد تم قبول طلب وإضافة التقييم الخارجي للخدمة");
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    public function cancel($id, Request $request)
    {
        $external_rating = ExternalRating::whereId($id)->first();
        if ($external_rating->status == 1) {
            return response()->error("لقد تم قبول الطلب سابقا", 403);
        }
        if ($external_rating->status == 2) {
            return response()->error("لقد تم رفض الطلب سابقا", 403);
        }
        try {
            DB::beginTransaction();
            $external_rating->status = 2;
            $external_rating->save();
            DB::commit();
            return response()->success("لقد تم رفض طلب التقييم الخارجي للخدمة");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    public function store(StoreExternalRatingRequest $request)
    {
        try {
            DB::beginTransaction();
            $external_rating = ExternalRating::create([
                "url" => $request->url,
                "status" => 0,
                "product_id" => $request->product_id
            ]);
            DB::commit();
            return response()->success("لقد تم إضافة طلب التقييم الخارجي للخدمة");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function update($id, StoreExternalRatingRequest $request)
    {
        $external_rating = ExternalRating::find($id);
        // شرط اذا كان العنصر موجود
        if (!$external_rating || !is_numeric($id)) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), 403);
        }

        try {
            DB::beginTransaction();
            $external_rating->update([
                "url" => $request->url,
            ]);
            DB::commit();
            return response()->success("لقد تم تعديل طلب التقييم الخارجي للخدمة");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
