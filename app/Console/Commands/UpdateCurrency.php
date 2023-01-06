<?php

namespace App\Console\Commands;

use App\Events\SendCurrency;
use App\Models\ApiCurrency;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_currency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "sharaf";
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
        return 0;
    }
}
