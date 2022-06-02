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
            if (!property_exists($value, 'country'))
                continue;
            $value->currency_id = $value->country->currency_id;
            $value->save();
        }
    }
}
