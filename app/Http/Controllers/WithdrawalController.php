<?php

namespace App\Http\Controllers;

use App\Events\AcceptWithdrwal;
use App\Events\CancelWithdrwal;
use App\Http\Requests\BankTransferWithdrawalRequest;
use App\Http\Requests\BankWithdrawalRequest;
use App\Http\Requests\PaypalWithdrawalRequest;
use App\Http\Requests\WiseWithdrawalRequest;
use App\Http\Requests\withdrawal\BankRequest;
use App\Http\Requests\withdrawal\BankTransferRequest;
use App\Http\Requests\withdrawal\PaypalRequest;
use App\Http\Requests\withdrawal\PaypalUpdateRequest;
use App\Http\Requests\withdrawal\WiseRequest;
use App\Http\Requests\withdrawal\WiseUpdateRequest;
use App\Models\Attachment;
use App\Models\BankAccount;
use App\Models\BankTransferDetail;
use App\Models\MoneyActivity;
use App\Models\PaypalAccount;
use App\Models\WiseAccount;
use App\Models\WiseCountry;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{

    // get countries wise

    public function countries()
    {
        $wise_countries = WiseCountry::all();
        return response()->success("لقد تمّ جلب البيانات بنجاح", $wise_countries);
    }
    // عرض جميع طلبات السحب

    public function index(Request $request)
    {
        $paginate = $request->query('paginate') ?? 10;
        $type = $request->query('type');
        $withdrawals = Withdrawal::where('status', $type)
            ->with('withdrawalable')
            ->paginate($paginate);
        return response()->success("لقد تمّ جلب البيانات بنجاح", $withdrawals);
    }

    // جلب طلب واحد

    public function show($id)
    {
        $withdrawal = Withdrawal::whereId($id)->with('withdrawalable')->first();
        return response()->success("لقد تمّ جلب البيانات بنجاح", $withdrawal);
    }

    // تغيير حالة الطلب بعد الموافقة عليه وارسال المبلغ المطلوب
    public function accept($id)
    {
        $withdrawal = Withdrawal::whereId($id)->without('wallet')->first();
        if ($withdrawal->status == 1) {
            return response()->error("لقد تم قبول الطلب سابقا", 403);
        }
        if ($withdrawal->status == 2) {
            return response()->error("لقد تم رفض الطلب سابقا", 403);
        }
        try {
            DB::beginTransaction();
            $withdrawal->status = 1;
            $withdrawal->save();
            // send notification to user
            $user = $withdrawal->wallet->profile->user;
            // اقتطاع مبلغ السحب
            $withdrawal->wallet->decrement('withdrawable_amount', $withdrawal->amount);
            $withdrawal->wallet->profile->decrement('withdrawable_amount', $withdrawal->amount);
            event(new AcceptWithdrwal($user, $withdrawal));
            DB::commit();
            return response()->success("لقد تم قبول طلب التحويل");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }

        // ارسال الاشعار
        return response()->success("لقد تمّ الموافقة على طلب السحب", $withdrawal);
    }


    public function cancel($id, Request $request)
    {
        $withdrawal = Withdrawal::whereId($id)->without('wallet')->first();
        if ($withdrawal->status == 1) {
            return response()->error("لقد تم قبول الطلب سابقا", 403);
        }
        if ($withdrawal->status == 2) {
            return response()->error("لقد تم رفض الطلب سابقا", 403);
        }
        try {
            DB::beginTransaction();
            $withdrawal->status = 2;
            $withdrawal->save();
            // send notification to user
            $user = $withdrawal->wallet->profile->user;

            event(new CancelWithdrwal($user, $withdrawal, $request->cause));
            DB::commit();
            return response()->success("لقد تم رفض طلب التحويل");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }

        // ارسال الاشعار
        return response()->success("لقد تمّ رفض طلب السحب", $withdrawal);
    }
    /* --------------------- تسجيل حسابات من اجل عملية السحب -------------------- */
    /**
     * store_paypal => انشاء حساب بايبال من اجل عملية السحب
     *
     * @param  mixed $request
     * @return void
     */
    public function store_paypal(PaypalRequest $request)
    {
        try {

            // جلب بيانات الحساب بايبال
            $paypal_account = PaypalAccount::where('profile_id', Auth::user()->profile->id)->first();
            // تحقق من وجود حساب بايبال
            if ($paypal_account) {
                return response()->error("لديك حساب بايبال فالموقع, تفقد بياناتك", 403);
            }

            // انشاء حساب بايبال من اجل عملية سحب
            $paypal_account = [
                'email' => $request->email,
                'profile_id' => Auth::user()->profile->id,
            ];
            DB::beginTransaction();
            // انشاء حساب بايبال
            $paypal_account = PaypalAccount::create($paypal_account);
            DB::commit();
            // نجاح العملية
            return response()->success("تمّ إضافة حساب باي بال بنجاح", $paypal_account);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
    /**
     * store_bank => تسجيل حساب بنكي
     *
     * @param  mixed $request
     * @return void
     */
    public function store_bank(BankRequest $request)
    {
        // جلب بيانات الحساب بنكي
        $account = BankAccount::where('profile_id', Auth::user()->profile->id)->first();
        // تحقق من وجود حساب بنكي
        if ($account) {
            return response()->error("لديك حساب بنكي فالموقع , تفقد بياناتك", 403);
        }
        try {
            // انشاء حساب بنك من اجل عملية سحب
            $data_bank_account = [
                'wise_country_id' => $request->wise_country_id,
                'full_name' => $request->full_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_adress_line_one' => $request->bank_adress_line_one,
                'bank_adress_line_two' => $request->bank_adress_line_two,
                'bank_swift' => $request->bank_swift,

                'bank_iban' => $request->bank_iban,
                'bank_number_account' => $request->bank_number_account,
                'phone_number_without_code' => $request->phone_number_without_code,
                'city' => $request->city,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
                'profile_id' => Auth::user()->profile->id,
            ];
            DB::beginTransaction();
            $bank_account = BankAccount::create($data_bank_account);
            DB::commit();
            // نجاح العملية
            return response()->success("تمّ إضافة حساب بنكي بنجاح", $bank_account);
        } catch (Exception $ex) {
            return $ex;
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
    /**
     * store_wise => انشاء حساب وايز من اجل عملية السحب
     *
     * @param  mixed $request
     * @return void
     */
    public function store_wise(WiseRequest $request)
    {
        try {
            // جلب بيانات الحساب وايز
            $account = WiseAccount::where('profile_id', Auth::user()->profile->id)->first();
            // تحقق من وجود حساب وايز
            if ($account) {
                return response()->error("لديك حساب وايز فالموقع , تفقد بياناتك", 403);
            }
            // انشاء حساب وايز من اجل عملية سحب
            $wise_account = [
                'email' => $request->email,
                'profile_id' => Auth::user()->profile->id,
            ];
            DB::beginTransaction();
            $wise_account = WiseAccount::create($wise_account);
            DB::commit();
            // نجاح العملية
            return response()->success("تمّ إضافة حساب وايز بنجاح", $wise_account);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * store_bank => انشاء حساب حوالة بنكي
     *
     * @param  mixed $request
     * @return void
     */
    public function store_bank_transfer(BankTransferRequest $request)
    {
        try {
            // جلب بيانات الحساب حوالة بنكية
            $account = BankTransferDetail::where('profile_id', Auth::user()->profile->id)->first();
            // تحقق من وجود حساب حوالة بنكية
            if ($account) {
                return response()->error("لديك حساب حوالة بنكية فالموقع , تفقد بياناتك", 403);
            }
            // انشاء حساب بنك من اجل عملية سحب
            $bank_transfer_account = [
                'country_id' => $request->country_id,
                'full_name' => $request->full_name,
                'city' => $request->city,
                'state' => $request->state,
                'phone_number_without_code' => $request->phone_number_without_code,
                'whatsapp_without_code' => $request->whatsapp_without_code,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
                'id_type' => $request->id_type,
                'profile_id' => Auth::user()->profile->id,
            ];

            $attachments = [];
            if ($request->attachments) {
                foreach ($request->file('attachments') as $key => $value) {
                    $attachmentPath = $value;
                    $attachmentName = 'attch-' . $key . Auth::user()->id .  time() . '.' . $attachmentPath->getClientOriginalExtension();
                    $attachmentPath->storePubliclyAs('attachments/bank_transfers', $attachmentName, 'do');
                    // تخزين معلومات المرفق
                    $attachments[$key] = ['path' => $attachmentName];
                }
            }
            DB::beginTransaction();
            $bank_transfer_detail = BankTransferDetail::create($bank_transfer_account);
            $bank_transfer_detail->attachments()->createMany($attachments);
            DB::commit();
            // نجاح العملية
            return response()->success("تمّ إضافة حساب حوالة البنكية بنجاح", $bank_transfer_detail);
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /* -------------------------------------------------------------------------- */

    // create withdrawals

    public function paypal(PaypalWithdrawalRequest $request)
    {
        $wallet = Auth::user()->profile->wallet;
        $withdrawable_amount = $wallet->withdrawable_amount;
        $pending_withdrawal_count = $wallet->withdrawals()
            ->where('status', 0)
            ->count();
        if ($pending_withdrawal_count > 0) {
            return response()->error('لديك عملية سحب معلّقة');
        }
        if ($withdrawable_amount < $request->amount) {
            return response()->error('رصيدك غير كاف لإجراء هذه العملية');
        }
        /*if ($request->amount < 10) {
            throw ValidationException::withMessages(['amount' => 'يجب أن يكون المبلغ 10 دولار فما فوق']);
        }*/
        try {
            //$paypal_account = Auth::user()->profile->paypal_account;

            DB::beginTransaction();

            PaypalAccount::create([
                'email' => $request->email
            ]);

            /*$withdrawal = $paypal_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_PAYPAL,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);*/

            /*$payload = [
                'title' => 'عملية طلب سحب بواسطة بايبال',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);*/

            DB::commit();
            //return response()->success("لقد تمّ إضافة طلبك بنجاح", $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function wise(WiseWithdrawalRequest $request)
    {
        $wallet = Auth::user()->profile->wallet;
        $withdrawable_amount = $wallet->withdrawable_amount;
        $pending_withdrawal_count = $wallet->withdrawals()
            ->where('status', 0)
            ->count();
        if ($pending_withdrawal_count > 0) {
            return response()->error('لديك عملية سحب معلّقة');
        }
        if ($withdrawable_amount < $request->amount) {
            return response()->error('رصيدك غير كاف لإجراء هذه العملية', 422);
        }

        if ($request->amount < 10) {
            throw ValidationException::withMessages(['amount' => 'يجب أن يكون المبلغ 10 دولار فما فوق']);
        }
        try {
            $wise_account = Auth::user()->profile->wise_account;

            DB::beginTransaction();
            $wise_account->update([
                'email' => $request->email
            ]);
            $withdrawal = $wise_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_WISE,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة وايز',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);

            DB::commit();
            return response()->success("لقد تمّ إضافة طلبك بنجاح", $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function bank(BankWithdrawalRequest $request)
    {
        $wallet = Auth::user()->profile->wallet;
        $withdrawable_amount = $wallet->withdrawable_amount;
        $pending_withdrawal_count = $wallet->withdrawals()
            ->where('status', 0)
            ->count();

        if ($pending_withdrawal_count > 0) {
            return response()->error('لديك عملية سحب معلّقة');
        }
        if ($withdrawable_amount < $request->amount) {
            return response()->error('رصيدك غير كاف لإجراء هذه العملية');
        }
        if ($request->amount < 10) {
            throw ValidationException::withMessages(['amount' => 'يجب أن يكون المبلغ 10 دولار فما فوق']);
        }
        try {
            $bank_account = Auth::user()->profile->bank_account;
            DB::beginTransaction();

            $bank_account->update([
                'wise_country_id' => $request->wise_country_id,
                'full_name' => $request->full_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_adress_line_one' => $request->bank_adress_line_one,
                'bank_adress_line_two' => $request->bank_adress_line_two,
                'bank_swift' => $request->bank_swift,

                'bank_iban' => $request->bank_iban,
                'bank_number_account' => $request->bank_number_account,
                'phone_number_without_code' => $request->phone_number_without_code,
                'city' => $request->city,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
            ]);
            $withdrawal = $bank_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_BANK,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة حساب بنكي',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);



            DB::commit();
            return response()->success("لقد تمّ إضافة طلبك بنجاح", $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function bank_transfer(BankTransferWithdrawalRequest $request)
    {
        $wallet = Auth::user()->profile->wallet;
        $withdrawable_amount = $wallet->withdrawable_amount;
        $pending_withdrawal_count = $wallet->withdrawals()
            ->where('status', 0)
            ->count();

        if ($pending_withdrawal_count > 0) {
            return response()->error('لديك عملية سحب معلّقة');
        }
        if ($withdrawable_amount < $request->amount) {
            return response()->error('رصيدك غير كاف لإجراء هذه العملية');
        }
        if ($request->amount < 10) {
            throw ValidationException::withMessages(['amount' => 'يجب أن يكون المبلغ 10 دولار فما فوق']);
        }

        try {
            $bank_transfer_detail = Auth::user()->profile->bank_transfer_detail;
            DB::beginTransaction();

            $bank_transfer_detail->update([
                'country_id' => $request->country_id,
                'full_name' => $request->full_name,
                'city' => $request->city,
                'state' => $request->state,
                'phone_number_without_code' => $request->phone_number_without_code,
                'whatsapp_without_code' => $request->whatsapp_without_code,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
                'id_type' => $request->id_type,
            ]);
            $withdrawal = $bank_transfer_detail->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_BANK_TRANSFER,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $attachments = [];
            if ($request->has('attachments')) {
                foreach ($request->file('attachments') as $key => $value) {
                    $attachmentPath = $value;
                    $attachmentName = 'bank_transfers/attch-' . $key . $bank_transfer_detail->id . Auth::user()->id .  time() . '.' . $attachmentPath->getClientOriginalExtension();
                    $path = Storage::putFileAs('attachments', $value, $attachmentName);
                    // تخزين معلومات المرفق
                    $attachments[$key] = ['path' => $attachmentName];
                }
                $bank_transfer_detail->attachments()->createMany($attachments);
            }

            $payload = [
                'title' => 'عملية طلب سحب بواسطة حوالة بنكية',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            // اقتطاع مبلغ السحب
            $wallet->decrement('withdrawable_amount', $withdrawal->amount);
            Auth::user()->profile->decrement('withdrawable_amount', $withdrawal->amount);

            DB::commit();
            return response()->success("لقد تمّ إضافة طلبك بنجاح", $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }


    /**
     * update_paypal => تحديث بيانات حساب باي بال
     *
     * @param  mixed $request
     * @return void
     */
    public function update_paypal(PaypalUpdateRequest $request)
    {
        try {
            // جلب بيانات الحساب بايبال
            $paypal_account = PaypalAccount::where('profile_id', Auth::user()->profile->id)->first();
            // تحقق من وجود حساب بايبال
            if (!$paypal_account) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            DB::beginTransaction();

            $paypal_account->update([
                'email' => $request->email
            ]);

            DB::commit();
            // اظهار العنصر
            return response()->success(__("messages.oprations.update_success"), $paypal_account);
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * update_wise => تحديث بيانات حساب وايز
     *
     * @param  mixed $request
     * @return void
     */
    public function update_wise(WiseUpdateRequest $request)
    {
        try {
            // جلب بيانات الحساب وايز
            $wise_account = WiseAccount::where('profile_id', Auth::user()->profile->id)->first();
            // تحقق من وجود حساب وايز
            if (!$wise_account) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            DB::beginTransaction();
            $wise_account->update([
                'email' => $request->email
            ]);

            DB::commit();
            // اظهار العنصر
            return response()->success(__("messages.oprations.update_success"), $wise_account);
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * update_bank => تحديث بيانات حساب بنك
     *
     * @param  mixed $request
     * @return void
     */
    public function update_bank(BankWithdrawalRequest $request)
    {
        try {
            // جلب بيانات الحساب بنكي
            $bank_account = BankAccount::where('profile_id', Auth::user()->profile->id)->first();

            // تحقق من وجود حساب بنكي
            if (!$bank_account) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }

            DB::beginTransaction();
            // تحديث بيانات الحساب بنكي
            $bank_account->update([
                'wise_country_id' => $request->wise_country_id,
                'full_name' => $request->full_name,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'bank_adress_line_one' => $request->bank_adress_line_one,
                'bank_adress_line_two' => $request->bank_adress_line_two,
                'bank_swift' => $request->bank_swift,

                'bank_iban' => $request->bank_iban,
                'bank_number_account' => $request->bank_number_account,
                'phone_number_without_code' => $request->phone_number_without_code,
                'city' => $request->city,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
            ]);
            DB::commit();
            // اظهار العنصر
            return response()->success(__("messages.oprations.update_success"), $bank_account);
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * update_bank_transfer => تحديث بيانات حساب الحوالة بنكية
     *
     * @param  mixed $request
     * @return void
     */
    public function update_bank_transfer(BankTransferWithdrawalRequest $request)
    {
        try {
            // جلب بيانات الحساب حوالة البنكية
            $bank_transfer_detail = BankTransferDetail::where('profile_id', Auth::user()->profile->id)
            ->first();

            // تحقق من وجود حساب حوالة بنكية
            if (!$bank_transfer_detail) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // وضع المعلومات فالصفوفة
            $data_bank_transfer = [
                'country_id' => $request->country_id,
                'full_name' => $request->full_name,
                'city' => $request->city,
                'state' => $request->state,
                'phone_number_without_code' => $request->phone_number_without_code,
                'whatsapp_without_code' => $request->whatsapp_without_code,
                'address_line_one' => $request->address_line_one,
                'address_line_two' => $request->address_line_two,
                'code_postal' => $request->code_postal,
                'id_type' => $request->id_type,
            ];


            $attachments = [];

            if ($request->hasFile('attachments')) {
                foreach ($bank_transfer_detail->attachments as $attachment) {
                    if (Storage::disk('do')->exists("attachments/bank_transfers/{$attachment['path']}")) {
                        Storage::disk('do')->delete("attachments/bank_transfers/{$attachment['path']}");
                    }
                }
                // وضع الملفات فالصفوفة
                foreach ($request->file('attachments') as $key => $value) {
                    $attachmentPath = $value;
                    $attachmentName = 'attch-' . $key . Auth::user()->id .  time() . '.' . $attachmentPath->getClientOriginalExtension();
                    $attachmentPath->storePubliclyAs('attachments/bank_transfers', $attachmentName, 'do');
                    // تخزين معلومات المرفق
                    $attachments[$key] = ['path' => $attachmentName];
                }
                //$bank_transfer_detail->attachments()->createMany($attachments);
            }

            DB::beginTransaction();
            // تحديث بيانات الحساب حوالة البنكية
            $bank_transfer_detail->update($data_bank_transfer);
            // شرط اذا كانت هناك مرفقات موجودة
            if ($request->hasFile('attachments')) {
                if (count($bank_transfer_detail->attachments) > 0) {
                    $bank_transfer_detail->attachments()->delete();
                }
                // تخزين معلومات المرفق
                $bank_transfer_detail->attachments()->createMany($attachments);
            }
            DB::commit();
            // اظهار العنصر
            return response()->success(__("messages.oprations.update_success"), $bank_transfer_detail);
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
