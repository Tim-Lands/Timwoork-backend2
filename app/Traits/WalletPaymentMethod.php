<?php

namespace App\Traits;

use App\Models\MoneyActivity;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait WalletPaymentMethod
{
    public function wallet_purchase($cart)
    {
        try {
            $user = Auth::user();
            $profile = $user->profile;
            $wallet = $profile->wallet;
            $withdrawable_amount = $wallet->withdrawable_amount;

            if ($cart->total_price > $withdrawable_amount) {
                return false;
            }

            $new_amount = $withdrawable_amount - $cart->total_price;

            DB::beginTransaction();

            $wallet->decrement('withdrawable_amount', $cart->total_price);
            $profile->decrement('withdrawable_amount', $cart->total_price);
            $payment = $cart->payments()->create([
                'payment_type' => 'wallet',
                'payload' => [
                    'price' => $cart->total_price,
                    'tax' => 0
                ],
            ]);

            $payload = [
                'title' => 'عملية شراء بواسطة المحفظة',
                'title_ar' => 'عملية شراء بواسطة المحفظة',
                'title_en' => 'Purchasing by wallet',
                'title_fr' => 'Achat par portefeuille',
                'payment_method' => 'المحفظة',
                'total_price' => $cart->total_price,
                'price_with_tax' => $cart->total_price,
                'tax' => 0,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => Auth::user()->profile->wallet->id,
                'amount' => $cart->total_price,
                'status' => MoneyActivity::STATUS_BUYING,
                'payload' => $payload,
            ]);

            if (!$payment) {
                $wallet->withdrawable_amount += $cart->total_price;
                $wallet->save();
                $profile->withdrawable_amount += $cart->total_price;
                $profile->save();
                return response()->error('لقد حدث خطأ في عملية تخزين معلومات الدفع، سيتم إرجاع المبلغ اليك');
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
