<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use File;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $countries_json = File::get("database/data/db_countries_currencis.json");
        $countries = json_decode($countries_json);
        foreach ($countries as $key => $value) {
            if (!property_exists($value, 'countryCode'))
                continue;
            $country_code = $value->countryCode;
            $currency = Currency::where("code", $value->currencyCode)
                ->first();
            if (is_null($currency))
                continue;
            $currency_id = $currency->id;
            Country::where('id', $value->id)->update([
                'name_en' => $value->name_en,
                'country_code' => $country_code,
                'currency_id' => $currency_id

            ]);
        }
    }
}
