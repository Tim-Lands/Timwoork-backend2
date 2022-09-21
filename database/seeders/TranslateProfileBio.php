<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\ProfileSeller;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

use function PHPUnit\Framework\isNull;

class TranslateProfileBio extends Seeder
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
        $profiles = ProfileSeller::all();
        foreach ($profiles as $profile) {
            if(is_null($profile->bio))
                continue;

            $profile->bio_ar = $profile->bio;
            $profile->bio_en = $tr->translate($profile->bio);
            $profile->save();
        }
        $tr->setTarget('fr');
        foreach ($profiles as $profile) {
            if(is_null($profile->bio))
                continue;
            $profile->bio_fr = $tr->translate($profile->bio);
            $profile->save();
        }    }
}
