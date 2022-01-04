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

    public function stripe_purchase(Request $request, $cart)
    {
        try {
            $user = User::find(Auth::id());
            $user->createOrGetStripeCustomer();
            $stripe_payment = $user->charge($cart->price_with_tax * 100, $request->payment_method_id)
                ->invoicePrice('price_tshirt', 5);
            $stripe_payment = $stripe_payment->asStripePaymentIntent();
            DB::beginTransaction();
            $payment = Payment::create([
                'payment_type' => 'stripe',
                'payload' => $stripe_payment,
            ]);
            DB::commit();
            return response()->success('نجحت عملية الدفع بواسطة البطاقة البنكية', $payment);
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
            return response()->error('فشلت عملية الدفع بواسطة البطاقة البنكية');
        }
    }
}
