<?php

namespace App\Http\Controllers;

use App\Events\AcceptWithdrwal;
use App\Http\Requests\BankTransferWithdrawalRequest;
use App\Http\Requests\BankWithdrawalRequest;
use App\Http\Requests\PaypalWithdrawalRequest;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalRequest;
use App\Http\Requests\WiseWithdrawalRequest;
use App\Models\BankTransferDetailAttachment;
use App\Models\MoneyActivity;
use App\Models\Withdrawal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{

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
        if ($withdrawal->status) {
            return response()->error("لقد تم قبول الطلب سابقا", 403);
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
        if ($request->amount != 0 && $request->amount < 10) {
            throw ValidationException::withMessages(['amount' => 'يجب أن يكون المبلغ 10 دولار فما فوق']);
        }
        try {
            $paypal_account = Auth::user()->profile->paypal_account;

            DB::beginTransaction();

            $paypal_account->update([
                'email' => $request->email
            ]);
            $withdrawal = $paypal_account->withdrawal()->create([
                'wallet_id' => $wallet->id,
                'type' => Withdrawal::TYPE_PAYPAL,
                'amount' => $request->amount ?? $wallet->withdrawable_amount,
                'status' => Withdrawal::PENDING_WITHDRAWAL,
            ]);

            $payload = [
                'title' => 'عملية طلب سحب بواسطة بايبال',
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
            return response()->error('رصيدك غير كاف لإجراء هذه العملية');
        }
        if ($request->amount != 0 && $request->amount < 10) {
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
                'amount' => $request->amount ?? $wallet->withdrawable_amount,
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
        if ($request->amount != 0 && $request->amount < 10) {
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
                'amount' => $request->amount ?? $wallet->withdrawable_amount,
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
        if ($request->amount != 0 && $request->amount < 10) {
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
                'amount' => $request->amount ?? $wallet->withdrawable_amount,
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

    // update details
    public function update_paypal(PaypalWithdrawalRequest $request)
    {

        try {
            $paypal_account = Auth::user()->profile->paypal_account;

            DB::beginTransaction();

            $paypal_account->update([
                'email' => $request->paypal_email
            ]);

            DB::commit();
            return response()->success("لقد تمّ التعديل بنجاح");
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function update_wise(WiseWithdrawalRequest $request)
    {
        try {
            $wise_account = Auth::user()->profile->wise_account;

            DB::beginTransaction();
            $wise_account->update([
                'wise_email' => $request->wise_email
            ]);

            DB::commit();
            return response()->success("لقد تمّ التعديل بنجاح");
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function update_bank(BankWithdrawalRequest $request)
    {
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
            DB::commit();
            return response()->success("لقد تمّ التعديل بنجاح");
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }

    public function update_bank_transfer(BankTransferWithdrawalRequest $request)
    {

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

            DB::commit();
            return response()->success("لقد تمّ التعديل بنجاح");
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error(__("messages.errors.error_database"), 403);
        }
    }
}
