<?php

namespace App\Http\Controllers;

use App\Events\SendCurrency;
use App\Models\ApiCurrency;
use App\Models\Currency;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;

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
            $currencies = db::table('currencies')->join('api_currencies', 'currencies.code', '=', 'api_currencies.code')->select('currencies.*')
                ->get();
            return response()->success('success', $currencies);
        } catch (Exception $e) {
            return response()->setStatusCode(500);
        }
        //
    }

    public function send_currency_values()
    {
        $data = ApiCurrency::all();
        return response()->success('success', $data);
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
        foreach ($data_currency as $item) {
            //ApiCurrency::where('code', $item['code'])->update(['value' => $item['value']]);
            ApiCurrency::updateOrCreate(
                ['code' => $item['code']],
                ['value' => $item['value']]
            );
        }
        // ارسال البيانات الى البوشر

        //event(new SendCurrency($data_currency));
        // send data to currency to pusher channel currency channel

        SendCurrency::dispatch($data_currency);


        // ارسال رسالة نجاح
        return response()->success('success', array_values($data_currency));
    }
}
