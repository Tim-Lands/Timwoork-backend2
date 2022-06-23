<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileLangSeederClass extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profiles = Profile::with('Country')->get();
        foreach ($profiles as $profile) {
            if (!is_null($profile->country))
                $profile->lang = $profile->country->lang;
                $profile->save();
        }
        //
    }
}
