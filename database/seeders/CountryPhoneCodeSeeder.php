<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use File;
class CountryPhoneCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_json = File::get("database/data/CountryCodes.json");
        $data = json_decode($data_json);
        foreach($data as $key => $value){
            $country = Country::where('country_code',$value->code)->first();
            if(is_null($country))
                continue;
            $country->code_phone = $value->dial_code;
            $country->save();
        }
    }
}
