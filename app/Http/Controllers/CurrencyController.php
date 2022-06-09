<?php

namespace App\Http\Controllers;

use App\Events\SendCurrency;
use App\Models\Currency;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (Cache::has('api_currency_data')) {
                $currency_data_api = Cache::get('api_currency_data');
                $currency_keys = array_keys($currency_data_api);
                $currencies = DB::table('currencies')->select('*')->whereIn('code', $currency_keys)->groupBy('code')->get();
            } else
                $currencies = DB::table('currencies')->select('*')->groupBy('code')->get();

            return response()->success('success', $currencies);
        } catch (Exception $e) {
            return response()->setStatusCode(500);
        }
        //
    }

    public function send_currency_values()
    {
        $data = Cache::get('api_currency_data');
        return response()->success('success', array_values($data));
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
        $data_currency = json_decode($data_currency, true)['data'];
        Cache::put('api_currency_data', $data_currency);
        // ارسال البيانات الى البوشر

        event(new SendCurrency(array_values($data_currency)));

        // ارسال رسالة نجاح
        return response()->success('success', array_values($data_currency));
    }
}
