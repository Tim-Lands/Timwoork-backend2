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
use App\Models\BankAccount;
use App\Models\BankTransferDetail;
use App\Models\MoneyActivity;
use App\Models\PaypalAccount;
use App\Models\Wallet;
use App\Models\WiseAccount;
use App\Models\WiseCountry;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Stichoza\GoogleTranslate\GoogleTranslate;

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


    /**
     * cancel => رفض طلب السحب
     *
     * @param  mixed $id
     * @param  mixed $request
     * @return void
     */
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
            $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            $xlocalization = "ar";
            if ($request->headers->has('X-localization'))
                $xlocalization = $request->header('X-localization');
            else {
                $tr->setSource();
                $tr->setTarget('en');
                $tr->translate($request->cause);
                $xlocalization = $tr->getLastDetectedSource();
            }
            $tr->setSource($xlocalization);
            $cause_ar = "";
            $cause_en = "";
            $cause_fr = "";
            DB::beginTransaction();
            switch ($xlocalization) {
                case "ar":
                    $tr->setTarget('en');
                    $cause_en = $tr->translate($request->cause);
                    $tr->setTarget('fr');
                    $cause_fr = $tr->translate($request->cause);
                    $cause_ar = $request->cause;
                    break;
                case 'en':
                    $tr->setTarget('ar');
                    $cause_ar = $tr->translate($request->cause);
                    $tr->setTarget('fr');
                    $cause_fr = $tr->translate($request->cause);
                    $cause_en = $request->cause;
                    break;
                case 'fr':
                    $tr->setTarget('en');
                    $cause_en = $tr->translate($request->cause);
                    $tr->setTarget('ar');
                    $cause_ar = $tr->translate($request->cause);
                    $cause_fr = $request->cause;
                    break;
            }
            $withdrawal->status = 2;
            $withdrawal->save();
            // send notification to user
            $user = $withdrawal->wallet->profile->user;

            event(new CancelWithdrwal($user, $withdrawal, $request->cause, $cause_ar, $cause_en, $cause_fr));
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

    /**
     * paypal_withdrawal => انشاء عملية سحب من حساب بايبال
     *
     * @param  mixed $request
     * @return void
     */
    public function withdrawal_paypal(PaypalWithdrawalRequest $request)
    {
        try {
            // جلب المحفظة المستخدمة
            $wallet = Wallet::where('profile_id', Auth::user()->profile->id)->first();

            // تحقق من وجود المحفظة
            if (!$wallet) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب الحساب القابل للسحب من المحفظة
            $withdrawable_amount = $wallet->withdrawable_amount;
            // جلب الرصيد المعلق من قبل
            $pending_withdrawal_count = $wallet->withdrawals()
                ->where('status', 0)
                ->count();
            // شرط اذا كان هناك رصيد معلق
            if ($pending_withdrawal_count > 0) {
                return response()->error(__("messages.bank.pending_withdrawal"), 403);
            }
            // شرط اذا كان المبلغ المطلوب اكبر من الرصيد المتاح
            if ($withdrawable_amount < $request->amount) {
                return response()->error(__("messages.bank.not_enough_balance"), 403);
            }
            // جلب بيانات الحساب البايبال
            $paypal_account = PaypalAccount::where('profile_id', Auth::user()->profile->id)->first();
            // فحص ان كان لا يوجد حساب
            if (!$paypal_account) {
                return response()->error(__("messages.bank.account_paypal_not_found"), 404);
            }
            DB::beginTransaction();
            // انشاء عملية سحب من حساب بايبال
            $withdrawal = $paypal_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_PAYPAL,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة بايبال',
                'title_ar'=>'عملية طلب سحب بواسطة بايبال',
                'title_en'=>'The process of requesting a withdrawal by PayPal',
                'title_fr'=>'Le processus de demande de retrait par PayPal',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);

            DB::commit();
            return response()->success(__("messages.bank.success_paypal_withdrawal"), $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * withdrawal_wise => انشاء عملية سحب من حساب
     *
     * @param  mixed $request
     * @return void
     */
    public function withdrawal_wise(WiseWithdrawalRequest $request)
    {
        try {
            // جلب المحف
            $wallet = Wallet::where('profile_id', Auth::user()->profile->id)->first();

            // تحقق من وجود المحفظة
            if (!$wallet) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب الحساب القابل للسحب من المحفظة
            $withdrawable_amount = $wallet->withdrawable_amount;
            // جلب الرصيد المعلق من قبل
            $pending_withdrawal_count = $wallet->withdrawals()
                ->where('status', 0)
                ->count();
            // شرط اذا كان هناك رصيد معلق
            if ($pending_withdrawal_count > 0) {
                return response()->error(__("messages.bank.pending_withdrawal"), 403);
            }
            // شرط اذا كان المبلغ المطلوب اكبر من الرصيد المتاح
            if ($withdrawable_amount < $request->amount) {
                return response()->error(__("messages.bank.not_enough_balance"), 403);
            }
            // جلب بيانات الحساب الوايز
            $wise_account = WiseAccount::where('profile_id', Auth::user()->profile->id)->first();
            // فحص ان كان لا يوجد حساب
            if (!$wise_account) {
                return response()->error(__("messages.bank.account_wise_not_found"), 404);
            }
            DB::beginTransaction();
            // انشاء عملية سحب من حساب وايز
            $withdrawal = $wise_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_WISE,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة وايز',
                'title_ar' => 'عملية طلب سحب بواسطة وايز',
                'title_en' => 'The process of requesting a withdrawal by WISE',
                'title_fr' => 'Processus de demande de retrait WISE',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);

            DB::commit();
            return response()->success(__("messages.bank.success_wise_withdrawal"), $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            //return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * withdrawal_bank => انشاء عملية سحب من حساب البنك
     *
     * @param  mixed $request
     * @return void
     */
    public function withdrawal_bank(BankWithdrawalRequest $request)
    {
        try {
            // جلب المحف
            $wallet = Wallet::where('profile_id', Auth::user()->profile->id)->first();

            // تحقق من وجود المحفظة
            if (!$wallet) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب الحساب القابل للسحب من المحفظة
            $withdrawable_amount = $wallet->withdrawable_amount;
            // جلب الرصيد المعلق من قبل
            $pending_withdrawal_count = $wallet->withdrawals()
                ->where('status', 0)
                ->count();
            // شرط اذا كان هناك رصيد معلق
            if ($pending_withdrawal_count > 0) {
                return response()->error(__("messages.bank.pending_withdrawal"), 403);
            }
            // شرط اذا كان المبلغ المطلوب اكبر من الرصيد المتاح
            if ($withdrawable_amount < $request->amount) {
                return response()->error(__("messages.bank.not_enough_balance"), 403);
            }
            // جلب بيانات الحساب البنكي
            $bank_account = BankAccount::where('profile_id', Auth::user()->profile->id)->first();
            // فحص ان كان لا يوجد حساب
            if (!$bank_account) {
                return response()->error(__("messages.bank.account_bank_not_found"), 404);
            }
            DB::beginTransaction();
            // انشاء عملية سحب من حساب بنكي
            $withdrawal = $bank_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_BANK,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة حساب بنكي',
                'title_ar' => 'عملية طلب سحب بواسطة حساب بنكي',
                'title_en' => 'The process of requesting a withdrawal by WISE',
                'title_fr' => 'Processus de demande de retrait WISE',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);

            DB::commit();

            return response()->success(__("messages.bank.success_bank_withdrawal"), $withdrawal->load('withdrawalable'));
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    /**
     * withdrawal_bank_transfer => انشاء عملية سحب من حساب الحوالة البنكية
     *
     * @param  mixed $request
     * @return void
     */
    public function withdrawal_bank_transfer(BankTransferWithdrawalRequest $request)
    {
        try {
            // جلب المحف
            $wallet = Wallet::where('profile_id', Auth::user()->profile->id)->first();

            // تحقق من وجود المحفظة
            if (!$wallet) {
                return response()->error(__("messages.errors.element_not_found"), 403);
            }
            // جلب الحساب القابل للسحب من المحفظة
            $withdrawable_amount = $wallet->withdrawable_amount;
            // جلب الرصيد المعلق من قبل
            $pending_withdrawal_count = $wallet->withdrawals()
                ->where('status', 0)
                ->count();
            // شرط اذا كان هناك رصيد معلق
            if ($pending_withdrawal_count > 0) {
                return response()->error(__("messages.bank.pending_withdrawal"), 403);
            }
            // شرط اذا كان المبلغ المطلوب اكبر من الرصيد المتاح
            if ($withdrawable_amount < $request->amount) {
                return response()->error(__("messages.bank.not_enough_balance"), 403);
            }
            // جلب بيانات الحساب البنكي
            $bank_transfer_detail = BankTransferDetail::where('profile_id', Auth::user()->profile->id)->first();
            // فحص ان كان لا يوجد حساب
            if (!$bank_transfer_detail) {
                return response()->error(__("messages.bank.account_bank_transfer_detail_not_found"), 404);
            }
            DB::beginTransaction();

            $withdrawal = $bank_transfer_detail->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_BANK_TRANSFER,
                'amount' => $request->amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة حوالة بنكية',
                'title_ar' => 'عملية طلب سحب بواسطة حوالة بنكية',
                'title_en' => 'Process of requesting withdrawal by bank transfer',
                'title_fr' => 'Processus de demande de retrait par virement bancaire',
                'amount' => $withdrawal->amount,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => $wallet->id,
                'amount' =>  $withdrawal->amount,
                'status' => MoneyActivity::STATUS_REFUND,
                'payload' => $payload,
            ]);
            DB::commit();

            return response()->success(__("messages.bank.success_bank_transfer_withdrawal"), $withdrawal->load('withdrawalable'));
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
