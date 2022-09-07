<?php

namespace Database\Seeders;

use Cog\Contracts\Ban\Ban;
use Cog\Laravel\Ban\Models\Ban as ModelsBan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        $bans = DB::table('bans')->get();
        foreach ($bans as $ban) {
            $comment_ar = $ban->comment;
            $comment_en = $tr->translate($ban->comment);
            DB::table('bans')
            ->where('id', $ban->id)
            ->update(['comment_ar'=>$comment_ar, 'comment_en'=>$comment_en]);
        }
        $tr->setTarget('fr');
        foreach ($bans as $ban) {
            $comment_fr = $tr->translate($ban->comment);
            DB::table('bans')
            ->where('id', $ban->id)
            ->update(['comment_fr'=>$comment_fr]);
        }
    }
}
