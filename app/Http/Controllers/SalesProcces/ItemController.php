<?php

namespace App\Http\Controllers\SalesProcces;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Exception;
use Illuminate\Http\Request;

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
     * ItemAcceptedBySeller
     *
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
     * item_rejected_by_seller => الغاء الطلبية من قبل البائع
     *
     * @return void
     */
    public function item_rejected_by_seller($id)
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
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_REJECTED_REQUEST;
                $item->save();
            } else {
                return response()->error('لا يمكن تغير هذه الحالة , تفقد بياناتك', 403);
            }
            // رسالة نجاح
            return response()
            ->success("{$item->profileSeller->profile->user->username} تم رفض الطلب من قبل البائع");
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error('هناك خطأ ما حدث في قاعدة بيانات , يرجى التأكد من ذلك', 403);
        }
    }
    
    /**
     * ModificationProjectbySeller => عمل على تعديلات من قبل البائع
     *
     * @return void
     */
    public function modification_resource_by_seller()
    {
        try {
        } catch (Exception $ex) {
        }
    }
    
    /**
     * delivery_resource_by_seller => تسليم المشروع
     *
     * @return void
     */
    public function delivery_resource_by_seller()
    {
        # code...
    }
    
    /**
     * RequestModificationResource => طلب تعديلات من قبل المشتري
     *
     * @return void
     */
    public function request_modification_resource_by_buyer($id)
    {
        try {
        } catch (Exception $ex) {
        }
    }
    
    /**
     * accepted_modification_resource_by_seller => موافقة على طلب تعديلات من قبل البائع
     *
     * @return void
     */
    public function accepted_modification_resource_by_seller()
    {
        # code...
    }
}
