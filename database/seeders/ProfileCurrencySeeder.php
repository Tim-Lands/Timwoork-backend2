<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $profiles = Profile::with('country')->get()->all();
        foreach ($profiles as $key => $value) {
            $country =  $value->country;
            if(is_null($country))
                continue;
            $value->currency_id = $country->currency_id;
            $value->save();

        }
    }
}
