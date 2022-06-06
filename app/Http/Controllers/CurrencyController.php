<?php

namespace App\Http\Controllers;

use App\Events\SendCurrency;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::all();
        return response()->success(_('success'), $currencies);
        //
    }

    /**
     * send_currency => ارسال البيانات العملات الى البوشر
     *
     * @param  mixed $request
     * @return void
     */
    public function send_currency()
    {
        $url = "https://api.currencyapi.com/v3/latest?apikey="
            . env('CURRENCY_API_KEY');
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $data_currency = curl_exec($curl);
        curl_close($curl);
        $data_currency = json_decode($data_currency, true);

        // ارسال البيانات الى البوشر
        event(new SendCurrency($data_currency));

        // ارسال رسالة نجاح
        return response()->success('success', $data_currency);
    }
}
