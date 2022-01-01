<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\ResourceRequest;
use App\Models\Item;
use App\Models\ItemOrderResource;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
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
        
    /**
     * item_accepted_by_seller => قبول الطلبية من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function item_accepted_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل قبولها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING_REQUEST) {
                // تحويل الطلبية من حالة الابتدائية الى حالة القبول
                $item->status = Item::STATUS_ACCEPT_REQUEST;
                $item->save();
            } else {
                return response()->error('لا يمكن تغير هذه الحالة , تفقد بياناتك', 403);
            }
            // رسالة نجاح
            return response()
            ->success("{$item->profileSeller->profile->user->username} تم قبول الطلب من قبل البائع");
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    
       
    /**
     * item_rejected_anyone  => الغاء الطلبية من قبل احد الطرفين
     *
     * @param  mixed $id
     * @return void
     */
    public function item_rejected_anyone($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING_REQUEST) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_REJECTED_REQUEST;
                $item->save();
            } else {
                // رسالة خطأ
                return response()->error('لا يمكن تغير هذه الحالة , تفقد بياناتك', 403);
            }
            // رسالة نجاح
            return response()->success(" تم رفض الطلب من قبل احد الطرفين");
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    
        
    /**
     * delivery_resource_by_seller => تسليم المشروع من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function delivery_resource_by_seller($id)
    {
        try {
            // جلب المشروع
            $item_resource = ItemOrderResource::find($id);
            // شرط اذا كان المشؤروع موجود
            if (!$item_resource) {
                // رسالة خطأ
                return response()->error('يجب عليك رفع المشروع قبل التسليم', 403);
            }

            // جلب حالة الطلبية
            $status_item = $item_resource->item->status;
            // شرط اذا كانت حالة الطلبية في قيد التنفيذ
            if ($status_item == Item::STATUS_ACCEPT_REQUEST) {
                if ($item_resource->status == 0) {
                    // وضع المشروع في حالة التسليم
                    $item_resource->status = ItemOrderResource::RESOURCE_DELIVERY;
                    $item_resource->save();
                } else {
                    // رسالة خطأ
                    return response()->error('لقد تم تسليم المشروع مسبقا, تفقد بياناتك', 403);
                }
            } else {
                return response()->error('لا يمكن تغير هذه الحالة , تفقد بياناتك', 403);
            }
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success('تم تسليم المشروع من قبل البائع');
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    
       
    /**
     * upload_resource_by_seller => رفع المشروع من قبل البائع
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function upload_resource_by_seller(ResourceRequest $request, $id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفع تالمشروع
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error('هذا العنصر غير موجود', 403);
            }
            $item_rousource = ItemOrderResource::where('item_id', $item->id)->first();
            if ($item_rousource) {
                // رسالة خطأ
                return response()->error('لقد تم رفع الملف من قبل , الان يجب عليك تسليم', 403);
            }
            // انشاء مصفوفة من اجل رفع المشروع
            $data_resource = [];
            // شرط اذا كانت الحالة قيد التنفيذ
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                $time = time();
                // جلب المشروع من المرسلات
                $file_resource = $request->file('file_resource');
                // وضع اسم جديد للمشروع
                $file_resource_name = "tw-resource-{$item->uuid}-{$time}.{$file_resource->getClientOriginalExtension()}";
                // رفع المشروع
                Storage::putFileAs('resources_files', $request->file('file_resource'), $file_resource_name);
                // وضع المشروع في المصفوفة
                $data_resource = [
                    'item_id'    => $item->id,
                    'path'       => $file_resource_name,
                    'full_path'  => $request->file('file_resource'),
                    'size'       => number_format($request->file('file_resource')->getSize() / 1048576, 3) . ' MB',
                    'mime_type'  => $request->file('file_resource')->getClientOriginalExtension(),
                ];
            } else {
                // رسالة خطأ
                return response()->error('لا يمكن رفع المشروع الا اذا كانت الطلبية في حالة التنفيذ , تفقد بياناتك', 403);
            }
            /* ---------------------- وضع المشروع في قواعد البيانات --------------------- */
            // بداية المعاملة مع قواعد البيانات
            DB::beginTransaction();
            // وضع المشروع في قواعد البيانات
            $item_order_reource = ItemOrderResource::create($data_resource);
            // انهاء المعاملة
            DB::commit();
            // رسالة نجاح عملية رفع المشروع:
            return response()->success('تم رفع المشروع و تهيئة تسليمه للمشتري', $item_order_reource);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    
    /**
     * accepted_delivery_resource_by_seller => قبول التسليم من قبل المشتري
     *
     * @return void
     */
    public function accepted_delivery_resource_by_buyer()
    {
        # code...
    }
    
    /**
     * rejected_delivery_resource_by_buyer => رفض التسليم من قبل المشتري
     *
     * @return void
     */
    public function rejected_delivery_resource_by_buyer()
    {
        # code...
    }
    
    /**
     * request_rejected_by_seller => طلب الغاء من قبل البائع
     *
     * @return void
     */
    public function request_reject_by_seller()
    {
        # code...
    }
    
    /**
     * request_rejected_by_buyer => طلب الغاء من قبل المشتري
     *
     * @return void
     */
    public function request_reject_by_buyer()
    {
        # code...
    }
    
    /**
     * accept_rejected_by_seller => قبول الغاء الطلبية من قبل البائع
     *
     * @return void
     */
    public function accept_rejected_by_seller()
    {
        # code...
    }
    
    /**
     * accept_rejected_by_buyer => قبول الغاء الطلبية من قبل المشتري
     *
     * @return void
     */
    public function accept_rejected_by_buyer()
    {
        # code...
    }
}
