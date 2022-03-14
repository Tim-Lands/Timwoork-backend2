<?php

namespace App\Http\Controllers\SalesProcces;

use App\Events\AcceptedDileveredByBuyer;
use App\Events\AcceptModifiedBySeller;
use App\Events\AcceptOrder;
use App\Events\AcceptRequestRejectOrder;
use App\Events\CanceledOrderByBuyer;
use App\Events\CanceledOrderBySeller;
use App\Events\DileveredBySeller;
use App\Events\RejectModifiedRequestBySeller;
use App\Events\RejectOrder;
use App\Events\RejectRequestRejectOrder;
use App\Events\RequestModifiedBuBuyer;
use App\Events\RequestRejectOrder;
use App\Events\ResolveConflictBySeller;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemAttachmentRequest;
use App\Models\Amount;
use App\Models\Item;
use App\Models\ItemOrderModified;
use App\Models\ItemOrderRejected;
use App\Models\MoneyActivity;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        // جلب الطلبية
        $product_id = Item::whereId($id)->first()->number_product;
        $item = Item::whereId($id)
            ->with([
                'order.cart.user.profile' => function ($q) {
                    $q->with(['level', 'badge']);
                },
                'profileSeller.profile',
                'profileSeller.products' => function ($q) use ($product_id) {
                    $q->select('id', 'profile_seller_id', 'buyer_instruct')->where('id', $product_id);
                },
                'profileSeller' => function ($q) {
                    $q->with(['level', 'badge']);
                },
                'item_rejected',
                'item_modified',
                'attachments',
                'item_date_expired',
                'conversation.messages.user.profile',
                'conversation.messages.attachments'
            ])
            ->first();
        if (!$item) {
            // رسالة خطأ
            return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
        }
        // رسالة نجاح
        return response()->success(__("messages.oprations.get_data"), $item);
    }

    /**
     * display_item_rejected
     *
     * @param  mixed $id
     * @return void
     */
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
     * & تم تصليحها
     */
    public function item_accepted_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل قبولها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status != Item::STATUS_PENDING) {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب مشتري الطلبية
            $buyer = $item->order->cart->user;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // تحويل الطلبية من حالة الابتدائية الى حالة القبول
            $item->status = Item::STATUS_ACCEPT;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->save();
            // ارسال اشعار
            event(new AcceptOrder($buyer, $item));
            DB::commit();
            // رسالة نجاح
            return response()
                ->success(__("messages.item.accept_item_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            //return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }


    /**
     * item_rejected_by_seller  => رفض من قبل البائع
     *
     * @param  mixed $id
     * @return void
     * & تم تصليحها
     */
    public function item_rejected_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status != Item::STATUS_PENDING) {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب مشتري الطلبية
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();

            // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
            $item->status = Item::STATUS_REJECTED_BY_SELLER;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->is_item_work = Item::IS_ITEM_NOT_WORk;
            $item->save();

            // انشاء مبلغ جديد
            $amount = Amount::create([
                'amount' => $item_amount,
                'wallet_id' => $wallet->id,
                'item_id' => $item->id,
                'status' => Amount::WITHDRAWABLE_AMOUNT
            ]);
            // تحويله الى محفظة المشتري
            $wallet->amounts()->save($amount);
            $wallet->withdrawable_amount += $item_amount;
            $wallet->save();
            $wallet->refresh();
            // تحديث بيانات المشتري
            $profile->withdrawable_amount += $item_amount;
            $profile->save();

            $payload = [
                'title' => 'استعادة مبلغ بعد رفض طلبية',
                'amount' => $item_amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' => $item_amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            event(new RejectOrder($buyer, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * item_cancelled_buyer  => الغاء الطلبية من قبل المشتري
     *
     * @param  mixed $id
     * @return void
     * & تم تصليحها
     */
    public function item_cancelled_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status != Item::STATUS_PENDING) {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب بيانات البائع
            $user = User::find($item->user_id);
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;

            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
            $item->status = Item::STATUS_CANCELLED_BY_BUYER;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->is_item_work = Item::IS_ITEM_NOT_WORk;
            $item->save();

            // تحويل مبلغ الطلبية الى محفظة المشتري
            // انشاء مبلغ جديد
            $amount = Amount::create([
                'amount' => $item_amount,
                'wallet_id' => $wallet->id,
                'item_id' => $item->id,
                'status' => Amount::WITHDRAWABLE_AMOUNT
            ]);
            // تحويله الى محفظة المشتري
            $wallet->amounts()->save($amount);
            $wallet->withdrawable_amount += $item_amount;
            $wallet->save();
            $wallet->refresh();
            // تحديث بيانات المشتري
            $profile->withdrawable_amount += $item_amount;
            $profile->save();
            $payload = [
                'title' => 'استعادة مبلغ بعد إلغاء طلبية',
                'amount' => $item_amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' => $item_amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            // إرسال إشعار
            event(new CanceledOrderByBuyer($user, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_buyer"));
        } catch (Exception $ex) {
            DB::rollBack();
            //return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * item_cancelled_by_seller  => الغاء الطلبية من قبل البائع
     *
     * @param  mixed $id
     * @return void
     * & تم تصليحها
     */
    public function item_cancelled_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل رفضها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_NOT_FOUND);
            }
            // شرط اذا كانت الحالة الطلبية في حالة الانتظار
            if ($item->status != Item::STATUS_ACCEPT) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // تحويل الطلبية من حالة الابتدائية الى حالة الرفض
            $item->status = Item::STATUS_CANCELLED_BY_SELLER;
            $item->is_rating = true;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->is_item_work = Item::IS_ITEM_NOT_WORk;
            $item->save();

            // تحويل مبلغ الطلبية الى محفظة المشتري

            // انشاء مبلغ جديد
            $amount = Amount::create([
                'amount' => $item_amount,
                'wallet_id' => $wallet->id,
                'item_id' => $item->id,
                'status' => Amount::WITHDRAWABLE_AMOUNT
            ]);
            // تحويله الى محفظة المشتري
            $wallet->amounts()->save($amount);
            $wallet->withdrawable_amount += $item_amount;
            $wallet->save();
            $wallet->refresh();
            // تحديث بيانات المشتري
            $profile->withdrawable_amount += $item_amount;
            $profile->save();

            $payload = [
                'title' => 'استعادة مبلغ بعد إلغاء طلبية',
                'amount' => $item_amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' => $item_amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            // إرسال إشعار
            event(new CanceledOrderBySeller($buyer, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.reject_item_by_buyer"));
        } catch (Exception $ex) {
            DB::rollBack();
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
     * & تم تصليحها
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
            // شرط اذا كانت الحالة قيد التنفيذ
            if ($item->status != Item::STATUS_ACCEPT) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            // انشاء مصفوفة من اجل رفع المشروع
            $data_resource = [];

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
            // إرسال الاشعار
            event(new DileveredBySeller($buyer, $item));
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
     * & تم تصليحها
     */
    public function accepted_delivery_by_buyer($id)
    {
        try {
            // جلب المشروع
            $item = Item::find($id);
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // شرط اذا كانت حالة الطلبية في قيد التسليم
            if ($item->status != Item::STATUS_DILEVERED) {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب بيانات البائع
            $seller = User::find($item->user_id);
            $profile = $seller->profile;
            //$precent_deducation = $profile->profile_seller->precent_deducation;
            $is_women = DB::table('products')->where('id', $item->number_product)->subcategory->is_women;
            if ($is_women) {
                $precent_deducation = 12;
            } else {
                $precent_deducation = 15;
            }
            $wallet = $seller->profile->wallet;
            $item_amount = ($item->price_product * $precent_deducation) / 100;
            $final_amount = $item->price_product - $item_amount;

            DB::beginTransaction();
            //  قبول المشروع اكتمال الطلبية
            $item->status = Item::STATUS_FINISHED;
            $item->is_rating = true;
            $item->is_item_work = Item::IS_ITEM_NOT_WORk;
            $item->save();

            // انشاء مبلغ جديد
            $amount = Amount::create([
                'amount' => $final_amount,
                'wallet_id' => $wallet->id,
                'item_id' => $item->id,
                'status' => Amount::PENDING_AMOUNT,
                'transfered_at' => Carbon::now()
                    ->addDays(3)
                    ->toDateTimeString(),
            ]);
            // تحويله الى محفظة المشتري
            $wallet->amounts()->save($amount);
            $wallet->amounts_pending += $final_amount;
            $wallet->save();
            $wallet->refresh();
            // تحديث بيانات المشتري
            $profile->pending_amount += $final_amount;
            $profile->save();

            $payload = [
                'title' => 'ربح المبلغ من طلبية',
                'amount' => $final_amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' => $final_amount,
                'status' => MoneyActivity::STATUS_EARNING,
                'payload' => $payload,
            ]);
            // زيادة عدد المشتريات
            DB::table('products')->where('id', $item->number_product)->increment('count_buying', 1);
            // ارسال الاشعار
            event(new AcceptedDileveredByBuyer($seller, $item));
            DB::commit();
            // رسالة نجاح عملية تسليم المشروع:
            return response()->success(__('messages.item.resource_dilevered'));
        } catch (Exception $ex) {
            DB::rollBack();
            //return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /* ------------------------------ الغاء الطلبية ----------------------------- */
    /**
     * request_cancel_item_by_buyer => طلب الغاء من قبل المشتري
     *
     * @return void
     * & تم تصليحها
     */
    public function request_cancel_item_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();

            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // شرط اذا كانت حالة الطلبية في قيد التنفيذ
            if ($item->status != Item::STATUS_ACCEPT) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // شرط اذا كان الطلب موجود من قيل
            if ($item_rejected) {
                return response()->error(__('messages.item.found_request_rejected'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // جلب  البائع
            $user = User::find($item->user_id);
            // وضع معلومات الالغاء الطلبية في مصفوفة
            $data_request_cancelled_by_buyer = [
                'status' => ItemOrderRejected::PENDING,
                'item_id'         => $item->id
            ];
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            ItemOrderRejected::create($data_request_cancelled_by_buyer);

            $item->status = Item::STATUS_CANCELLED_REQUEST_BUYER;
            $item->date_expired = Carbon::now()->addDays(Item::EXPIRED_TIME_NNTIL_SOME_DAYS)->toDateTimeString();
            $item->save();
            // ارسال الاشعار
            event(new RequestRejectOrder($user, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.request_buyer_success"));
        } catch (Exception $ex) {
            DB::rollBack();
            return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * accept_cancel_request_by_seller => قبول الغاء الطلبية من قبل البائع
     *
     * @return void
     * & تم تصليحها
     */
    public function accept_cancel_request_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status != Item::STATUS_CANCELLED_REQUEST_BUYER) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
            if (!$item_rejected || $item_rejected->status != ItemOrderRejected::PENDING) {
                return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;
            $profile = $buyer->profile;
            $wallet = $buyer->profile->wallet;
            $item_amount = $item->price_product;


            // وضع معلومات قبول الالغاء الطلبية في مصفوفة
            $data_accept_request_by_seller = [
                'status' => ItemOrderRejected::ACCEPTED,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */

            DB::beginTransaction();
            // عملية قبول طلب الغاء الطلبية
            $item_rejected->update($data_accept_request_by_seller);

            $item_rejected->delete();
            // رفض الطلبية
            $item->status = Item::STATUS_CANCELLED_BY_BUYER;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->is_item_work = Item::IS_ITEM_NOT_WORk;
            $item->save();
            // انشاء مبلغ جديد
            $amount = Amount::create([
                'amount' => $item_amount,
                'wallet_id' => $wallet->id,
                'item_id' => $item->id,
                'status' => Amount::WITHDRAWABLE_AMOUNT
            ]);
            // تحويله الى محفظة المشتري
            $wallet->amounts()->save($amount);
            $wallet->withdrawable_amount += $item_amount;
            $wallet->save();
            $wallet->refresh();
            // تحديث بيانات المشتري
            $profile->withdrawable_amount += $item_amount;
            $profile->save();

            $payload = [
                'title' => 'استعادة مبلغ بعد إلغاء طلبية',
                'amount' => $item_amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' => $item_amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            // إرسال الاشعار
            event(new AcceptRequestRejectOrder($buyer, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.request_accepted_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * reject_request_by_seller => رفض الغاء الطلبية من قبل البائع
     *
     * @return void
     * & تم تصليحها
     */
    public function reject_cancel_request_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // شرط اذا كانت الحالة الطلبية في حالة قيد التنفيذ
            if ($item->status != Item::STATUS_CANCELLED_REQUEST_BUYER) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
            if (!$item_rejected || $item_rejected->status != ItemOrderRejected::PENDING) {
                return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
            }
            // جلب البائع
            $user = $item->order->cart->user;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // عملية رفض طلب الغاء الطلبية
            $item_rejected->update(['status' =>  ItemOrderRejected::REJECTED]);
            // عملية التعليق الطلبية
            $item->status = Item::STATUS_SUSPEND;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;

            $item->save();
            // ارسال الاشعار
            event(new RejectRequestRejectOrder($user, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.request_rejected_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * resolve_the_conflict_between_them_in_rejected =>   حل النزاع بين الطرفين في حالة الغاء الطلبية
     *
     * @return void
     * & تم تصليحها
     */
    public function resolve_the_conflict_between_them_in_rejected($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }

            if ($item->status != Item::STATUS_SUSPEND) {
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_rejected = ItemOrderRejected::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
            if (!$item_rejected || $item_rejected->status != ItemOrderRejected::REJECTED) {
                return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
            }
            // جلب البائع
            $user = $item->order->cart->user;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // عملية رفع طلب الغاء الطلبية
            $item_rejected->delete();
            // عملية قيد التنفيذ الطلبية
            $item->status = Item::STATUS_ACCEPT;
            $item->save();
            // ارسال الاشعار
            event(new ResolveConflictBySeller($user, $item));
            //  event(new RejectRequestRejectOrder($user, $item)); // عبد الله
            DB::commit();

            // رسالة نجاح
            return response()->success(__("messages.item.resolve_the_conflict_between_them"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /* --------------------------------  طلب تعديل المشروع ------------------------------- */
    /**
     * request_modified_by_buyer => طلب تعديل من قبل المشتري
     *
     * @return void
     * & تم تصليحها
     */
    public function request_modified_by_buyer($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            if ($item->status != Item::STATUS_DILEVERED) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_NOT_FOUND);
            }
            // جلب طلب الغاء الخدمة
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();
            // شرط اذا كان الطلب موجود من قيل
            if ($item_modified) {
                return response()->error(__('messages.item.found_request_modified'), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // جلب  البائع
            $user = User::find($item->user_id);
            // وضع معلومات الالغاء الطلبية في مصفوفة
            $data_request_modified_by_buyer = [
                'status' => ItemOrderModified::PENDING,
                'item_id'         => $item->id
            ];

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            ItemOrderModified::create($data_request_modified_by_buyer);
            $item->date_expired = Carbon::now()
                ->addDays(Item::EXPIRED_TIME_NNTIL_SOME_DAYS)
                ->toDateTimeString();

            $item->status = Item::STATUS_MODIFIED_REQUEST_BUYER;
            $item->date_expired = Carbon::now()
                ->addDays(Item::EXPIRED_TIME_NNTIL_SOME_DAYS)
                ->toDateTimeString();
            $item->save();
            // ارسال الاشعار
            event(new RequestModifiedBuBuyer($user, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.re"));
        } catch (Exception $ex) {
            DB::rollBack();
            //return $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * accept_modified_by_seller => قبول التعديل من قبل البائع
     *
     * @return void
     * & تم تصليحها
     */
    public function accept_modified_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            // شرط اذا كانت الحالة الطلبية في حالة طلب تعديل
            if ($item->status != Item::STATUS_MODIFIED_REQUEST_BUYER) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب طلب الغاء الخدمة
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف المشتري
            if (!$item_modified || $item_modified->status != ItemOrderModified::PENDING) {
                return response()->error(__('messages.item.request_not_found'), Response::HTTP_FORBIDDEN);
            }
            // جلب بيانات المشتري
            $buyer = $item->order->cart->user;

            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();

            $item_modified->delete();
            //  الطلبية قيد التنفيذ
            $item->status = Item::STATUS_ACCEPT;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->save();
            // إرسال الاشعار
            event(new AcceptModifiedBySeller($buyer, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.accepted_modified_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * reject_modified_by_seller => رفض التعديل من قبل البائع
     *
     * @return void
     * & تم تصليحها
     */
    public function reject_modified_by_seller($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }
            if ($item->status != Item::STATUS_MODIFIED_REQUEST_BUYER) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر التعديل
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
            if (!$item_modified || $item_modified->status != ItemOrderModified::PENDING) {
                return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب البائع
            $user = $item->order->cart->user;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // عملية رفض التعديل
            $item_modified->update(['status' =>  ItemOrderModified::REJECTED]);

            // عملية التعليق الطلبية
            $item->status = Item::STATUS_SUSPEND_CAUSE_MODIFIED;
            $item->date_expired = Item::EXPIRED_ITEM_NULLABLE;
            $item->save();
            // ارسال الاشعار
            event(new RejectModifiedRequestBySeller($user, $item));
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.reject_modified_by_seller"));
        } catch (Exception $ex) {
            DB::rollBack();
            //return  $ex;
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }
    /**
     * resolve_the_conflict_between_them_in_modified =>   حل النزاع بين الطرفين في حالة التعديل
     *
     * @return void
     * & تم تصليحها
     */
    public function resolve_the_conflict_between_them_in_modified($id)
    {
        try {
            // جلب عنصر الطلبية من اجل طلب الغائها
            $item = Item::whereId($id)->first();
            // شرط اذا كانت متواجدة
            if (!$item) {
                // رسالة خطأ
                return response()->error(__("messages.errors.element_not_found"), Response::HTTP_FORBIDDEN);
            }

            if ($item->status != Item::STATUS_SUSPEND_CAUSE_MODIFIED) {
                // رسالة خطأ
                return response()->error(__("messages.item.not_may_this_operation"), Response::HTTP_FORBIDDEN);
            }
            // جلب عنصر الطلب
            $item_modified = ItemOrderModified::where('item_id', $item->id)->first();
            // شرط اذا كان هناك طلب الغاء و ايضا ارسال عملية طلب من طرف البائع
            if (!$item_modified || $item_modified->status != ItemOrderModified::REJECTED) {
                return response()->error(__("messages.item.request_not_found"), Response::HTTP_FORBIDDEN);
            }
            // جلب البائع
            $user = $item->order->cart->user;
            /* --------------------------- تغيير حالة الطلبية --------------------------- */
            DB::beginTransaction();
            // عملية رفع طلب الغاء الطلبية
            $item_modified->delete();
            // عملية قيد التنفيذ الطلبية
            $item->status = Item::STATUS_ACCEPT;
            $item->save();
            // ارسال الاشعار
            event(new ResolveConflictBySeller($user, $item)); // عبد الله
            DB::commit();
            // رسالة نجاح
            return response()->success(__("messages.item.resolve_the_conflict_between_them"));
        } catch (Exception $ex) {
            DB::rollBack();
            // رسالة خطأ
            return response()->error(__("messages.errors.error_database"), Response::HTTP_FORBIDDEN);
        }
    }

    /* -------------------------------------------------------------------------- */
}
