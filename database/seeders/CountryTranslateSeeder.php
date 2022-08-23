<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CountryTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tr = new GoogleTranslate();
        $tr->setSource('ar');
        $tr->setTarget('fr');
        $countries = Country::all();
        foreach($countries as $country){
            $country->name_fr = $tr->translate($country->name_ar);
            $country->save();
        }
        $tr->setTarget('en');
        foreach($countries as $country){
            $country->name_en = $tr->translate($country->name_ar);
            $country->save();
        }
    }
}
