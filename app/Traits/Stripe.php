<?php

namespace App\Traits;

use App\Models\MoneyActivity;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait Stripe
{
    public function stripe_purchase(Request $request, $cart)
    {
        try {
            $user = User::find(Auth::id());
            $user->createOrGetStripeCustomer();
            $stripe_payment = $user->charge($cart->price_with_tax * 100, $request->payment_method_id);
            $stripe_payment = $stripe_payment->asStripePaymentIntent();
            DB::beginTransaction();
            $payment = $cart->payments()->create([
                'payment_type' => 'stripe',
                'payload' => $stripe_payment,
            ]);
            $payload = [
                'title' => 'عملية شراء',
                'payment_method' => 'stripe',
                'total_price' => $cart->total_price,
                'price_with_tax' => $cart->price_with_tax,
                'tax' => $cart->tax,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => Auth::user()->profile->wallet->id,
                'amount' => $cart->price_with_tax,
                'status' => MoneyActivity::STATUS_BUYING,
                'payload' => json_encode($payload, JSON_PRETTY_PRINT)
            ]);
            if (!$payment) {
                $user->refund($stripe_payment->id);
                return response()->error('لقد حدث خطأ في عملية تخزين معلومات الدفع، سيتم إرجاع المبلغ اليك');
            }
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error('فشلت عملية الدفع بواسطة البطاقة البنكية');
        }
    }
}
