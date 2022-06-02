<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use File;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies_json = File::get("database/data/db_currencies.json");
        $currencies = json_decode($currencies_json);
        foreach ($currencies as $key => $value) {
            Currency::create([
                "symbol" => $value->symbol,
                "symbol_native" => $value->symbol_native,
                "name" => $value->name,
                "code" => $value->code
            ]);
        };
        //
    }
}
