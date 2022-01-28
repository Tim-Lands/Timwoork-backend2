<?php

namespace App\Http\Controllers\SalesProcces;

use App\Events\AcceptOrder;
use App\Events\AcceptRequestRejectOrder;
use App\Events\RejectOrder;
use App\Events\RejectRequestRejectOrder;
use App\Events\RequestRejectOrder;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemAttachmentRequest;
use App\Models\Amount;
use App\Models\Item;
use App\Models\ItemOrderModified;
use App\Models\ItemOrderRejected;
use App\Models\User;
use Exception;
use Illuminate\Http\Response;
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
            ->with(['order.cart.user.profile', 'profileSeller.profile', 'item_rejected', 'item_modified', 'attachments', 'conversation'])
            ->first();
        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
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
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING) {
                // تحويل الطلبية من حالة الابتدائية الى حالة القبول
                $item->status = Item::STATUS_ACCEPT;
                $item->save();
                event(new AcceptOrder($user, $item));
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // رسالة نجاح
            return response()
                ->success(__("messages.item.accept_item_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }


    /**
     * item_rejected_by_seller  => رفض من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function item_rejected_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // جلب مشتري الطلبية
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_REJECTED_BY_SELLER;
                $item->save();
                event(new RejectOrder($user, $item));
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * item_cancelled_buyer  => الغاء الطلبية من قبل المشتري
     *
     * @param  mixed $id
     * @return void
     */
    public function item_cancelled_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();

            // جلب بيانات البائع
            $user = User::find($item->user_id);

            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;
            // شرط اذا كانت متواجدة

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_CANCELLED_BY_BUYER;
                $item->save();

                // تحويل مبلغ الطلبية الى محفظة المشتري

                // انشاء مبلغ جديد
                $amount = Amount::create([
                    'amount' => $item_amount,
                    'item_id' => $item->id,
                    'status' => Amount::WITHDRAWABLE_AMOUNT
                ]);
                // تحويله الى محفظة المشتري
                $wallet->amounts()->save($amount);
                $wallet->refresh();
                // تحديث بيانات المشتري
                $profile->withdrawable_amount += $item_amount;
                $profile->save();

                // إرسال إشعار
                event(new RejectOrder($user, $item));
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_buyer"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * item_cancelled_by_seller  => الغاء الطلبية من قبل البائع
     *
     * @param  mixed $id
     * @return void
     */
    public function item_cancelled_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();

            // جلب بيانات البائع
            $user = User::find($item->user_id);

            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;
            // شرط اذا كانت متواجدة

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status == Item::STATUS_PENDING) {
                // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
                $item->status = Item::STATUS_CANCELLED_BY_SELLER;
                $item->save();

                // تحويل مبلغ الطلبية الى محفظة المشتري

                // انشاء مبلغ جديد
                $amount = Amount::create([
                    'amount' => $item_amount,
                    'item_id' => $item->id,
                    'status' => Amount::WITHDRAWABLE_AMOUNT
                ]);
                // تحويله الى محفظة المشتري
                $wallet->amounts()->save($amount);
                $wallet->refresh();
                // تحديث بيانات المشتري
                $profile->withdrawable_amount += $item_amount;
                $profile->save();

                // إرسال إشعار
                event(new RejectOrder($user, $item)); // تصحيح من طرف عبد الله
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_buyer"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /* -------------------------------------------------------------------------- */
    /**
     * upload_resource_by_seller => رفع المشروع و تسليم من قبل البائع
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function dilevered_by_seller(ItemAttachmentRequest $request, $id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفع المشروع
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // انشاء مصفوفة من اجل رفع المشروع
            $data_resource = [];
            // شرط اذا كانت الحالة قيد التنفيذ
            if ($item->status == Item::STATUS_ACCEPT) {
                // جلب المشروع من المرسلات
                if ($request->has('item_attachments')) {
                    $item_attachments = $request->file('item_attachments');

                    foreach ($item_attachments as $key => $value) {
                        $time = time();
                        $file_attachment = "tw-attachment-{$item->uuid}-{$time}.{$value->getClientOriginalExtension()}";
                        // رفع المشروع
                        Storage::putFileAs('resources_files', $value, $file_attachment);
                        // وضع المشروع في المصفوفة
                        $data_resource[$key] = [
                            'item_id'    => $item->id,
                            'name'       => $file_attachment,
                            'path'  => $value,
                            'size'       => number_format($value->getSize() / 1048576, 3) . ' MB',
                            'mime_type'  => $value->getClientOriginalExtension(),
                        ];
                    }
                }
                // وضع اسم جديد للمشروع
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            /* ---------------------- وضع المشروع في قواعد البيانات --------------------- */
            // بداية المعاملة مع قواعد البيانات
            DB::beginTransaction();
            // شرط اذا كانت المصفوفة اكبر من 0
            if (count($data_resource) > 0) {
                // اضافة ملفات المشروع
                $item->attachments()->createMany($data_resource);
                // تغير حالة الطلبية الى تم الاستلام
                $item->status = Item::STATUS_DILEVERED;
                $item->save();
            } else {
                // تغير حالة الطلبية الى تم الاستلام
                $item->status = Item::STATUS_DILEVERED;
                $item->save();
            }

            // انهاء المعاملة
            DB::commit();
            // رسالة نجاح عملية رفع المشروع:
            return response()->success(__("messages.item.dilevery_resources_success"), $data_resource);
            /* -------------------------------------------------------------------------- */
        } catch (Exception $ex) {
            // لم تتم المعاملة بشكل نهائي و لن يتم ادخال اي بيانات لقاعدة البيانات
            DB::rollback();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * accepted_delivery => قبول التسليم المشروع من قبل المشتري
     *
     * @return void
     */
    public function accepted_delivery_by_buyer($id)
    {
        try {
            // جلب المشروع
            $item = Item::find($id);
            // شرط اذا كانت حالة الطلبية في قيد التنفيذ
            if ($item->status == Item::STATUS_DILEVERED) {
                //  قبول المشروع اكتمال الطلبية
                $item->status = Item::STATUS_FINISHED;
                $item->save();

                // عبد الله ابعث الدراهم للسيد يرحم والديك و متنساش الاقتطاع
            } else {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success(__('messages.item.resource_dilevered'));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /* ------------------------------ الغاء الطلبية ----------------------------- */
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

            // جلب  البائع
            $user = User::find($item->user_id);
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // شرط اذا كان الطلب موجود من قيل
            if ($item_rejected) {
                return response()->error(__('messages.item.found_request_rejected'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // وضع معلومات الالغاء الطلبية في مصفوفة
            $data_request_cancelled_by_buyer = [
                'status' => ItemOrderRejected::PENDING,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_ACCEPT) {
                ItemOrderRejected::create($data_request_cancelled_by_buyer);

                $item->status = Item::STATUS_CANCELLED_REQUEST_BUYER;
                $item->save();
                // ارسال الاشعار
                event(new RequestRejectOrder($user, $item));
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_buyer_success"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
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
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            // وضع معلومات قبول الالغاء الطلبية في مصفوفة
            $data_accept_request_by_seller = [
                'status' => ItemOrderRejected::ACCEPTED,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status == Item::STATUS_CANCELLED_REQUEST_BUYER) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
                if ($item_rejected && $item_rejected->status == ItemOrderRejected::PENDING) {
                    // عملية قبول طلب الغاء الطلبية
                    //$item_rejected->update($data_accept_request_by_seller);

                    $item_rejected->delete();
                    // رفض الطلبية
                    $item->status = Item::STATUS_CANCELLED_BY_BUYER;
                    $item->save();

                    // تحويل مبلغ الطلبية الى محفظة المشتري

                    // انشاء مبلغ جديد
                    $amount = Amount::create([
                        'amount' => $item_amount,
                        'item_id' => $item->id,
                        'status' => Amount::WITHDRAWABLE_AMOUNT
                    ]);
                    // تحويله الى محفظة المشتري
                    $wallet->amounts()->save($amount);
                    $wallet->refresh();
                    // تحديث بيانات المشتري
                    $profile->withdrawable_amount += $item_amount;
                    $profile->save();
                    // إرسال الاشعار
                    event(new AcceptRequestRejectOrder($buyer, $item));
                } else {
                    return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_accepted_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

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
            // جلب البائع
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر الطلب
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_CANCELLED_REQUEST_BUYER) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_rejected && $item_rejected->status == ItemOrderRejected::PENDING) {
                    // عملية رفع طلب الغاء الطلبية
                    $item_rejected->update(['status' =>  ItemOrderRejected::REJECTED]);

                    // عملية التعليق الطلبية
                    $item->status = Item::STATUS_SUSPEND;
                    $item->save();

                    // ارسال الاشعار
                    event(new RejectRequestRejectOrder($user, $item));
                } else {
                    return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.request_rejected_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * resolve_the_conflict_between_them_in_rejected =>   حل النزاع بين الطرفين في حالة الغاء الطلبية
     *
     * @return void
     */
    public function resolve_the_conflict_between_them_in_rejected($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // جلب البائع
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر الطلب
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_SUSPEND) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_rejected && $item_rejected->status == ItemOrderRejected::REJECTED) {
                    // عملية رفع طلب الغاء الطلبية
                    $item_rejected->delete();
                    // عملية قيد التنفيذ الطلبية
                    $item->status = Item::STATUS_ACCEPT;
                    $item->save();
                    // ارسال الاشعار
                    //  event(new RejectRequestRejectOrder($user, $item)); // عبد الله
                } else {
                    return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.resolve_the_conflict_between_them"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /* --------------------------------  طلب تعديل المشروع ------------------------------- */
    /**
     * request_cancel_item_by_buyer => طلب الغاء من قبل المشتري
     *
     * @return void
     */
    public function request_modified_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();

            // جلب  البائع
            $user = User::find($item->user_id);
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();
            // شرط اذا كان الطلب موجود من قيل
            if ($item_modified) {
                return response()->error(__('messages.item.found_request_modified'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // وضع معلومات الالغاء الطلبية في مصفوفة
            $data_request_modified_by_buyer = [
                'status' => ItemOrderModified::PENDING,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_DILEVERED) {
                ItemOrderModified::create($data_request_modified_by_buyer);

                $item->status = Item::STATUS_MODIFIED_REQUEST_BUYER;
                $item->save();
                // ارسال الاشعار
                //event(new RequestRejectOrder($user, $item)); // تعديل في الاشعار لعبد الله
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.re"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * accept_modified_by_seller => قبول التعديل من قبل البائع
     *
     * @return void
     */
    public function accept_modified_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();

            // وضع معلومات قبول الالغاء الطلبية في مصفوفة
            $data_accept_request_by_seller = [
                'status' => ItemOrderModified::ACCEPTED,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status == Item::STATUS_MODIFIED_REQUEST_BUYER) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
                if ($item_modified && $item_modified->status == ItemOrderModified::PENDING) {
                    // عملية قبول طلب الغاء الطلبية
                    // $item_modified->update($data_accept_request_by_seller);

                    $item_modified->delete();
                    //  الطلبية قيد التنفيذ
                    $item->status = Item::STATUS_ACCEPT;
                    $item->save();

                    // إرسال الاشعار
                    //event(new AcceptRequestRejectOrder($buyer, $item));
                } else {
                    return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.accepted_modified_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * reject_modified_by_seller => رفض التعديل من قبل البائع
     *
     * @return void
     */
    public function reject_modified_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // جلب البائع
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر التعديل
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_MODIFIED_REQUEST_BUYER) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_modified && $item_modified->status == ItemOrderModified::PENDING) {
                    // عملية رفض التعديل
                    $item_modified->update(['status' =>  ItemOrderModified::REJECTED]);
                    // عملية التعليق الطلبية
                    $item->status = Item::STATUS_SUSPEND_CAUSE_MODIFIED;
                    $item->save();

                    // ارسال الاشعار
                    //event(new RejectRequestRejectOrder($user, $item));
                } else {
                    return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.reject_modified_by_seller"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * resolve_the_conflict_between_them_in_modified =>   حل النزاع بين الطرفين في حالة التعديل
     *
     * @return void
     */
    public function resolve_the_conflict_between_them_in_modified($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // جلب البائع
            $user = $item->order->cart->user;
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر الطلب
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            if ($item->status == Item::STATUS_SUSPEND_CAUSE_MODIFIED) {
                // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
                if ($item_modified && $item_modified->status == ItemOrderModified::REJECTED) {
                    // عملية رفع طلب الغاء الطلبية

                    $item_modified->delete();
                    // عملية قيد التنفيذ الطلبية
                    $item->status = Item::STATUS_ACCEPT;
                    $item->save();
                    // ارسال الاشعار
                    //  event(new RejectRequestRejectOrder($user, $item)); // عبد الله
                } else {
                    return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
                }
            } else {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // رسالة نجاح
            return response()->success(__("messages.item.resolve_the_conflict_between_them"));
        } catch (Exception $ex) {
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /* -------------------------------------------------------------------------- */
}
