<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('badges')->insert([
            'name_ar' => 'وسام موثوقية الموقع',
            'name_en' => 'badge site reliability',
            'name_fr' => 'badge de la fiabilité du site',
        ]);

        DB::table('badges')->insert([
            'name_ar' => 'وسام مشتري VIP',
            'name_en' => 'badge VIP buyer',
            'name_fr' => 'badge acheteur VIP',
        ]);
    }
}
