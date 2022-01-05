<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;

trait Paypal
{

    public $return_url = 'http://localhost:3000/purchase/paypal?return=1';
    public $cancel_url = 'http://localhost:3000/purchase/paypal?return=0';

    public function approve($cart)
    {
        $request_body = [
            'intent' => 'CAPTURE',
            'application_context' =>
            [
                'return_url' => $this->return_url,
                'cancel_url' => $this->cancel_url,
                'brand_name' => 'Timwoork',
                'locale' => 'en-US',
                'landing_page' => 'BILLING',
                'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                'user_action' => 'PAY_NOW',
            ],
            'purchase_units' =>
            [
                0 =>
                [
                    'reference_id' => 'PUHF',
                    'description' => 'Sporting Goods',
                    'custom_id' => 'CUST-HighFashions',
                    'soft_descriptor' => 'HighFashions',
                    'amount' =>
                    [
                        'currency_code' => 'USD',
                        'value' => $cart->price_with_tax,
                        'breakdown' =>
                        [
                            'item_total' =>
                            [
                                'currency_code' => 'USD',
                                'value' => $cart->total_price,
                            ],

                            'tax_total' =>
                            [
                                'currency_code' => 'USD',
                                'value' => $cart->tax,
                            ],
                        ],
                    ],
                    'shipping' =>
                    [
                        'method' => 'United States Postal Service',
                        'name' =>
                        [
                            'full_name' => 'John Doe',
                        ],
                        'address' =>
                        [
                            'address_line_1' => '123 Townsend St',
                            'address_line_2' => 'Floor 6',
                            'admin_area_2' => 'San Francisco',
                            'admin_area_1' => 'CA',
                            'postal_code' => '94107',
                            'country_code' => 'US',
                        ],
                    ],
                ],
            ],

        ];

        foreach ($cart->cart_items as $key => $value) {

            $request_body['purchase_units'][0]['items'][$key]['name'] = $value->product_title;
            $request_body['purchase_units'][0]['items'][$key]['unit_amount']['currency_code'] = 'USD';
            $request_body['purchase_units'][0]['items'][$key]['unit_amount']['value'] = $value->price_product;
            $request_body['purchase_units'][0]['items'][$key]['quantity'] = $value->quantity;
        }

        //return $request_body;
        $client = app('paypal.client');
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = $request_body;
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            //return response()->json($response);
            if ($response->statusCode == 201) {
                foreach ($response->result->links as $link) {
                    if ($link->rel == 'approve') {
                        return response()->json($link->href);
                    }
                }
            }
        } catch (HttpException $ex) {
            return response()->error('حدث خطأ أثناء التحضير لعملية الدفع بواسطة بايبال');
            //return response()->json($ex);
        }
    }

    public function paypal_purchase($paypal_id, $cart)
    {

        if ($cart && $cart->is_buying) {
            return response()->error('السلة مباعة');
        }
        $client = app('paypal.client');
        $request = new OrdersCaptureRequest($paypal_id);
        $request->prefer('return=representation');
        try {
            $response = $client->execute($request);
            if ($response->statusCode == 201 && $response->result->status == 'COMPLETED') {
                // حفظ البيانات القادمة من 
                DB::beginTransaction();
                $payment = $cart->payments()->create([
                    'payment_type' => 'paypal',
                    'payload' => json_encode($response->result, JSON_PRETTY_PRINT)
                ]);

                DB::commit();
                return true;
            }
        } catch (HttpException $ex) {
            DB::rollBack();
            return response()->error('حدث خطأ أثناء عملية الدفع بواسطة بايبال');
        }
    }
}
