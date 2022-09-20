<?php

namespace Database\Seeders;

use App\Models\MoneyActivity;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateWalletActivitiesSeeder extends Seeder
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
        $activities = MoneyActivity::all();
        foreach ($activities as $activitie) {
            print($activitie->id);
            $data = $activitie->payload;
            $data['title_ar'] = $data['title'];
            $data['title_en'] = $tr->translate($data['title']);
            $activitie->payload = $data;
            $activitie->save();
        }
        $tr->setTarget('fr');
        foreach ($activities as $activitie) {
            $data = $activitie->payload;
            $data['title_fr'] = $tr->translate($data['title']);
            $activitie->payload = $data;
            $activitie->save();
        }
    }
}
