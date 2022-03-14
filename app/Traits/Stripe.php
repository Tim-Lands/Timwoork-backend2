<?php

namespace App\Traits;

use App\Models\MoneyActivity;
use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
                'title' => 'عملية شراء بواسطة بطاقة بنكية',
                'payment_method' => 'البطاقة بنكية',
                'total_price' => $cart->total_price,
                'price_with_tax' => $cart->price_with_tax,
                'tax' => $cart->tax,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => Auth::user()->profile->wallet->id,
                'amount' => $cart->price_with_tax,
                'status' => MoneyActivity::STATUS_BUYING,
                'payload' => $payload,
            ]);
            if (!$payment) {
                $user->refund($stripe_payment->id);
                return false;
            }
            DB::commit();
            return true;
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        }
    }
}
