<?php

namespace App\Traits;

use App\Models\Payment;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait Stripe
{
    use CreateOrder;

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
            if (!$payment) {
                $user->refund($stripe_payment->id);
                return response()->error('لقد حدث خطأ في عملية تخزين معلومات الدفع، سيتم إرجاع المبلغ اليك');
            }
            // وضع السلة مباعة
            $cart->is_buying = 1;
            $cart->save();
            DB::commit();
            return $this->create_order_with_items();
            /*             return response()->success('نجحت عملية الدفع بواسطة البطاقة البنكية', [
                'cart' => $cart,
                'payment' => $payment
            ]); */
        } catch (Exception $ex) {
            DB::rollback();
            // return $ex;
            return response()->error('فشلت عملية الدفع بواسطة البطاقة البنكية');
        }
    }
}
