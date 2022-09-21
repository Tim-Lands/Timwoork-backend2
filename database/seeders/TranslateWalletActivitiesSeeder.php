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
            $data = $activitie->payload;
            if (!isset($data['payment_method']))
                continue;
            $data['payment_method_ar'] = $data['payment_method'];
            $data['payment_method_en'] = $tr->translate($data['payment_method']);
            if ($data['payment_method'] == 'المحفظة')
                $data['payment_method_en'] = 'wallet';
            $activitie->payload = $data;
            $activitie->save();
        }
        $tr->setTarget('fr');
        foreach ($activities as $activitie) {
            $data = $activitie->payload;
            if (!isset($data['payment_method']))
                continue;
            if ($data['payment_method'] == 'المحفظة')
                $data['payment_method_fr'] = 'porte monnaie';
            if ($data['payment_method_en'] == 'stripe')
                $data['payment_method_fr'] = 'stripe';
            $activitie->payload = $data;
            $activitie->save();
        }
        /* foreach ($activities as $activitie) {
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
        } */
    }
}
