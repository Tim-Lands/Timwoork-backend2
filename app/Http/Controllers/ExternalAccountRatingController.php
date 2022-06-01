<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExternalAccountRatingRequest;
use App\Http\Requests\UpdateExternalAccountRatingRequest;
use App\Models\ExternalAccountRating;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalAccountRatingController extends Controller
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ?? 10;
        $type = $request->query('type');
        $rattings = ExternalAccountRating::where('status', $type)
            ->with('profileSeller.profile')
            ->paginate($paginate);
        return response()->success("لقد تمّ جلب البيانات بنجاح", $rattings);
    }

    public function show($id)
    {
        $external_rating = ExternalAccountRating::find($id);
        return response()->success("لقد تمّ جلب البيانات بنجاح", $external_rating);
    }

    public function accept($id, Request $request)
    {
        $external_rating = ExternalAccountRating::whereId($id)->first();
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
            $external_rating->profileSeller->external_rating = $request->external_rating;
            $external_rating->profileSeller->save();
            DB::commit();
            return response()->success("لقد تم قبول طلب وإضافة التقييم الخارجي للبروفايل");
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    public function cancel($id, Request $request)
    {
        $external_rating = ExternalAccountRating::whereId($id)->first();
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
            return response()->success("لقد تم رفض طلب التقييم الخارجي للبروفايل");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function store(StoreExternalAccountRatingRequest $request)
    {
        try {
            DB::beginTransaction();
            $external_rating = ExternalAccountRating::create([
                "url" => $request->url,
                "status" => 0,
                "profile_seller_id" => Auth::user()->profile->profileSeller->id
            ]);
            DB::commit();
            return response()->success("لقد تم إضافة طلب التقييم الخارجي للبروفايل");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function update($id, StoreExternalAccountRatingRequest $request)
    {
        $external_rating = ExternalAccountRating::find($id);
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
            return response()->success("لقد تم تعديل طلب التقييم الخارجي للبروفايل");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
