<?php

namespace App\Traits;

use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait WalletPaymentMethod
{
    public function wallet_purchase($cart)
    {
        try {
            DB::beginTransaction();
            $user = User::find(Auth::id());
            $profile = $user->profile;
            $wallet = $profile->wallet;
            $withdrawable_amount = $wallet->withdrawable_amount;
            if ($cart->total_price < $withdrawable_amount) {
                $new_amount = $withdrawable_amount - $cart->total_price;
                $wallet->withdrawable_amount = $new_amount;
                $wallet->save();
                $profile->withdrawable_amount = $new_amount;
                $profile->save();
                $payment = $cart->payments()->create([
                    'payment_type' => 'wallet',
                    'payload' => [
                        'price' => $cart->total_price,
                        'tax' => 0
                    ],
                ]);
                if (!$payment) {
                    $wallet->withdrawable_amount += $cart->total_price;
                    $wallet->save();
                    $profile->withdrawable_amount += $cart->total_price;
                    $profile->save();
                    return response()->error('لقد حدث خطأ في عملية تخزين معلومات الدفع، سيتم إرجاع المبلغ اليك');
                }
            } else {
                return response()->error('رصيدك لا يكفي لإتمام هذه العملية');
            }

            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error('فشلت عملية الدفع بواسطة المحفظة');
        }
    }
}
