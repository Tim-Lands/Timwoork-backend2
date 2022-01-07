<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerBadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seller_badges')->insert([
            'name_ar' => 'وسام موثوقية الموقع',
            'name_en' => 'badge site reliability',
            'name_fr' => 'badge de la fiabilité du site',
            'precent_deducation' => 12,
        ]);

        DB::table('seller_badges')->insert([
            'name_ar' => 'وسام ربات البيوت',
            'name_en' => 'badge Housewives',
            'name_fr' => 'badge femmes au foyer',
            'precent_deducation' => 10,
        ]);

        DB::table('seller_badges')->insert([
            'name_ar' => 'وسام أصحاب الهمم',
            'name_en' => 'badge people of determination',
            'name_fr' => 'badge des gens déterminésr',
            'precent_deducation' => 8,
        ]);
    }
}
