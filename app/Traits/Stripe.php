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
use Laravel\Cashier\Exceptions\IncompletePayment;

trait Stripe
{
    public function stripe_purchase(Request $request, $cart)
    {
        try {
            $user = User::find(Auth::id());
            $user->createOrGetStripeCustomer();
            $stripe_payment = $user->charge($cart->stripe()->total_with_tax * 100, $request->payment_method_id);
            $stripe_payment = $stripe_payment->asStripePaymentIntent();

            DB::beginTransaction();
            $payment = $cart->payments()->create([
                'payment_type' => 'stripe',
                'payload' => $stripe_payment,
            ]);
            $payload = [
                'title' => 'عملية شراء بواسطة بطاقة بنكية',
                'title_ar' => 'عملية شراء بواسطة بطاقة بنكية',
                'title_en' => 'Purchasing with a bank card',
                'title_fr' => 'Achat avec une carte bancaire',
                'payment_method' => 'البطاقة بنكية',
                'payment_method_ar' => 'البطاقة بنكية',
                'payment_method_en' => 'Bank card',
                'payment_method_fr' => 'carte bancaire',
                'total_price' => $cart->stripe()->total,
                'price_with_tax' => $cart->stripe()->total_with_tax,
                'tax' => $cart->stripe()->tax,
            ];
            $activity = MoneyActivity::create([
                'wallet_id' => Auth::user()->profile->wallet->id,
                'amount' => $cart->stripe()->total_with_tax,
                'status' => MoneyActivity::STATUS_BUYING,
                'payload' => $payload,
            ]);
            if (!$payment) {
                $user->refund($stripe_payment->id);
                return false;
            }
            DB::commit();
            return true;
        } catch (IncompletePayment $exception) {
            DB::rollback();
            return $exception->payment->status;
            //return  $exception->payment->status;
            //return response()->error(__('messages.errors.error_database'), Response::HTTP_FORBIDDEN);
        } catch (\Stripe\Exception\CardException $e) {
            // Too many requests made to the API too quickly
        }
    }
}
