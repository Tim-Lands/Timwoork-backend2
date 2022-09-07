<?php

namespace Database\Seeders;

use Cog\Laravel\Ban\Models\Ban;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateBanSeeder extends Seeder
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
        $bans = Ban::all();
        foreach ($bans as $ban) {

            $ban->comment_ar = $ban->comment;
            $ban->comment_en = $tr->translate($ban->comment);
            $ban->save();
        }
        $tr->setTarget('fr');
        foreach ($bans as $ban) {
            $ban->comment_fr = $tr->translate($ban->comment);
            $ban->save();
        }
    }
}
