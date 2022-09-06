<?php

namespace Database\Seeders;

use App\Models\Development;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateDevelopmentSeeder extends Seeder
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
        $tr->setTarget('en');
        $developments = Development::all();
        foreach ($developments as $development) {

            $development->title_ar = $development->title;
            $development->title_en = $tr->translate($development->title);
            $development->save();
        }
        $tr->setTarget('fr');
        foreach ($developments as $development) {
            $development->title_fr = $tr->translate($development->title);
            $development->save();
        }
    }
}
