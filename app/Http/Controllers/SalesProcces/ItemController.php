<?php

namespace App\Http\Controllers\SalesProcces;

use App\Events\AcceptOrder;
use App\Events\RejectOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesProcces\ResourceRequest;
use App\Models\Item;
use App\Models\ItemOrderRejected;
use App\Models\ItemOrderResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
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

    public function show($id)
    {
        // جلب الطلبية
        $item = Item::whereId($id)
            ->with(['order.cart.user', 'profileSeller'])
            ->whereHas('order.cart', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->first();
        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), 422);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }

    public function display_item_rejected($id)
    {
        $display = ItemOrderRejected::where('item_id', $id)->first();
        if ($display) {
            // رسالة نجاح
            return response()->success(__("messages.item.not_may_this_operation"), $display);
        } else {
            return response()->success(__('messages.item.not_found_item_reject'));
        }
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
            // جلب مشتري الطلبية
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING_REQUEST) {
                // تحويل الطلبية من حالة الابتدائية الى حالة القبول
                $item->status = Item::STATUS_ACCEPT_REQUEST;
                $item->save();
                event(new AcceptOrder($user, $item));
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()
                ->success(__("messages.item.accept_item_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    /**
     * item_rejected_seller  => الغاء الطلبية من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function item_rejected_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // جلب مشتري الطلبية
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 422);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING_REQUEST) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_REJECTED_BY_SELLER;
                $item->save();
                event(new RejectOrder($user, $item));
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * item_rejected_buyer  => الغاء الطلبية من قبل المشتري
     *
     * @param  mixed $id
     * @return void
     */
    public function item_rejected_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();

            // جلب بيانات البائع
            $user = User::find($item->user_id);
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 422);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING_REQUEST) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_REJECTED_BY_BUYER;
                $item->save();
                event(new RejectOrder($user, $item));
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_buyer"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
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
                return response()->error(__("messages.item.must_be_dilevery_resources"), 422);
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
                    return response()->error(__("messages.item.dilevery_resources_founded"), 422);
                }
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success(__("messages.item.dilevery_resources_success"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
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
            // جلب عنصر الطلبية من اجل رفع المشروع
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            $item_rousource = ItemOrderResource::where('item_id', $item->id)->first();
            if ($item_rousource) {
                // رسالة خطأ
                return response()->error(__('messages.item.resource_uploaded'), 403);
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
                return response()->error(__("messages.item.not_may_this_operation"), 422);
            }
            /* ---------------------- وضع المشروع في قواعد البيانات --------------------- */
            // بداية المعاملة مع قواعد البيانات
            DB::beginTransaction();
            // وضع المشروع في قواعد البيانات
            $item_order_reource = ItemOrderResource::create($data_resource);
            // انهاء المعاملة
            DB::commit();
            // رسالة نجاح عملية رفع المشروع:
            return response()->success(__("resource_upload"), $item_order_reource);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * accepted_delivery_resource_by_seller => قبول التسليم المشروع من قبل المشتري
     *
     * @return void
     */
    public function accepted_delivery_resource_by_buyer($id)
    {
        try {
            // جلب المشروع
            $item_resource = ItemOrderResource::find($id);
            // شرط اذا كان المشؤروع موجود
            if (!$item_resource) {
                // رسالة خطأ
                return response()->error(__("messages.item.resource_not_found"), 422);
            }

            // جلب حالة الطلبية
            $status_item = $item_resource->item->status;
            // شرط اذا كانت حالة الطلبية في قيد التنفيذ
            if ($status_item == Item::STATUS_ACCEPT_REQUEST) {
                if ($item_resource->status == ItemOrderResource::RESOURCE_DELIVERY) {
                    // قبول تسليم المشروع
                    $item_resource->status = ItemOrderResource::RESOURCE_ACCEPTED;
                    $item_resource->save();
                    // اكتمال الطلبية
                    $item_resource->item->status = Item::STATUS_FINISHED;
                    $item_resource->item->save();
                } elseif ($item_resource->status == ItemOrderResource::RESOURCE_REJECTED) {
                    // رسالة خطأ
                    return response()->error(__("messages.item.resource_rejected"), 422);
                } else {
                    // رسالة خطأ
                    return response()->error(__("messages.item.resource_not_dilevery"), 422);
                }
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), 422);
            }
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success(__('messages.item.resource_dilevered'));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * rejected_delivery_resource_by_buyer => رفض الاستلام المشروع من قبل المشتري
     *
     * @return void
     */
    public function rejected_delivery_resource_by_buyer($id)
    {
        try {
            // جلب المشروع
            $item_resource = ItemOrderResource::find($id);
            // شرط اذا كان المشروع موجود
            if (!$item_resource) {
                // رسالة خطأ
                return response()->error(__('messages.item.resource_not_found'), 422);
            }

            // جلب حالة الطلبية
            $status_item = $item_resource->item->status;
            // شرط اذا كانت حالة الطلبية في قيد التنفيذ
            if ($status_item == Item::STATUS_ACCEPT_REQUEST) {
                if ($item_resource->status == ItemOrderResource::RESOURCE_DELIVERY) {
                    // رفض تسليم المشروع
                    $item_resource->status = ItemOrderResource::RESOURCE_REJECTED;
                    $item_resource->save();
                    // الطلبية الطلبية
                    $item_resource->item->status = Item::STATUS_REJECTED_REQUEST;
                    $item_resource->item->save();
                } elseif ($item_resource->status == ItemOrderResource::RESOURCE_ACCEPTED) {
                    // رسالة خطأ
                    return response()->error(__("messages.item.resource_accepted"), 403);
                } else {
                    // رسالة خطأ
                    return response()->error(__("messages.item.resource_uploaded"), 403);
                }
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success(__("messages.item.resource_not_dilevered"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /* -------------------- طلب الغاء الطلبية من قبل الطرفين -------------------- */

    /**
     * request_cancel_item_by_seller => طلب الغاء من قبل البائع
     *
     * @return void
     */
    public function request_cancel_item_by_seller($id)
    {
        try {

            // جلب عنصر الطلبية من اجل طلب الغاء
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            $data_request_rejected_by_seller = [
                'rejected_seller' => ItemOrderRejected::REJECTED_BY_SELLER,
                'item_id'         => $item->id
            ];
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                // شرط اذا كان تم ارسال الطلب من قبل المشتري
                if ($item_rejected && $item_rejected->rejected_buyer == ItemOrderRejected::REJECTED_BY_BUYER) {
                    return response()->error(__("messages.item.request_buyer_sended"), 422);
                }

                if ($item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_SELLER) {
                    // عملية طلب الغاء الطلبية
                    return response()->error(__("messages.item.request_sended"), 422);
                } else {
                    if ($item_rejected) {
                        $item_rejected->update($data_request_rejected_by_seller);
                    } else {
                        // عملية طلب الغاء الطلبية
                        ItemOrderRejected::create($data_request_rejected_by_seller);
                    }
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__('messages.item.request_seller_success'));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * request_cancel_item_by_buyer => طلب الغاء من قبل المشتري
     *
     * @return void
     */
    public function request_cancel_item_by_buyer($id)
    {
        try {

            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // وضع معلومات الالغاء الطلبية في مصفوفة
            $data_request_rejected_by_buyer = [
                'rejected_buyer' => ItemOrderRejected::REJECTED_BY_BUYER,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                if ($item_rejected && $item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_SELLER) {
                    return response()->error(__("messages.item.request_seller_sended"), 422);
                }
                if ($item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_BUYER) {
                    // عملية طلب الغاء الطلبية
                    return response()->error(__("messages.item.request_sended"), 422);
                } else {
                    if ($item_rejected) {
                        $item_rejected->update($data_request_rejected_by_buyer);
                    } else {
                        // عملية طلب الغاء الطلبية
                        ItemOrderRejected::create($data_request_rejected_by_buyer);
                    }
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 422);
            }

            // رسالة نجاح
            return response()->success(__("messages.item.request_buyer_success"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
    /* ----------------------- قبول الطلبية من قبل الطرفين ---------------------- */

    /**
     * accept_cancel_request_by_seller => قبول الغاء الطلبية من قبل البائع
     *
     * @return void
     */
    public function accept_cancel_request_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // وضع معلومات قبول الالغاء الطلبية في مصفوفة
            $data_accept_request_by_seller = [
                'rejected_seller' => ItemOrderRejected::REJECTED_BY_SELLER,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
                if ($item_rejected && $item_rejected->rejected_buyer == ItemOrderRejected::REJECTED_BY_BUYER) {
                    if ($item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_SELLER) {
                        return response()->error(__("messages.item.request_sended"), 422);
                    }
                    // عملية قبول طلب الغاء الطلبية
                    $item_rejected->update($data_accept_request_by_seller);
                    // رفض الطلبية
                    $item->status = Item::STATUS_REJECTED_REQUEST;
                    $item->save();
                } else {
                    return response()->error(__('messages.item.request_not_found'), 403);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_accepted_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * accept_rejected_by_buyer => قبول الغاء الطلبية من قبل المشتري
     *
     * @return void
     */
    public function accept_cancel_request_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب عنصر الطلب
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // وضع معلومات قبول الالغاء الطلبية في مصفوفة
            $data_accept_request_by_buyer = [
                'rejected_buyer' => ItemOrderRejected::REJECTED_BY_BUYER,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_rejected && $item_rejected->rejected_buyer == ItemOrderRejected::REJECTED_BY_BUYER) {
                    if ($item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_BUYER) {
                        return response()->error(__("messages.item.request_sended"), 403);
                    }
                    // عملية قبول طلب الغاء الطلبية
                    $item_rejected->update($data_accept_request_by_buyer);
                    // رفض الطلبية
                    $item->status = Item::STATUS_REJECTED_REQUEST;
                    $item->save();
                } else {
                    return response()->error(__("messages.item.request_not_found"), 403);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_buyer_success"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /* ----------------------- رفض الطلبية من قبل الطرفين ----------------------- */

    /**
     * reject_request_by_seller => رفض الغاء الطلبية من قبل البائع
     *
     * @return void
     */
    public function reject_cancel_request_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب عنصر الطلب
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_rejected && $item_rejected->rejected_buyer == ItemOrderRejected::REJECTED_BY_BUYER) {
                    if ($item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_SELLER) {
                        return response()->error(__("messages.item.request_sended"), 403);
                    }
                    // عملية رفض طلب الغاء الطلبية
                    $item_rejected->update(['rejected_buyer' => 0]);
                } else {
                    return response()->error(__("messages.item.request_not_found"), 403);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), 403);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_rejected_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * reject_request_by_buyer => رفض الغاء الطلبية من قبل المشتري
     *
     * @return void
     */
    public function reject_cancel_request_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب عنصر الطلب
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            /* ---------------------------  حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_ACCEPT_REQUEST) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_rejected && $item_rejected->rejected_seller == ItemOrderRejected::REJECTED_BY_SELLER) {
                    if ($item_rejected->rejected_buyer == ItemOrderRejected::REJECTED_BY_BUYER) {
                        return response()->error(422, __("messages.item.request_sended"));
                    }
                    // عملية رفض طلب الغاء الطلبية
                    $item_rejected->update(['rejected_seller' => 0]);
                } else {
                    return response()->error(422, __("messages.item.request_not_found"));
                }
            } else {
                // رسالة خطأ
                return response()->error(422, __("messages.item.not_may_this_operation"));
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_rejected_by_buyer"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
    /* -------------------------------------------------------------------------- */
}
