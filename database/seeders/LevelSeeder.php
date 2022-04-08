<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels')->insert([
            'name_ar' => 'مشتري جديد',
            'name_en' => 'new buyer',
            'name_fr' => 'nouvel acheteur',
            'value_bayer_min' => 1,
            'value_bayer_max' => 99
        ]);
        DB::table('levels')->insert([
            'name_ar' => 'مشتري جاد',
            'name_en' => 'serious buyer',
            'name_fr' => 'acheteur sérieux',
            'value_bayer_min' => 100,
            'value_bayer_max' => 299
        ]);
        DB::table('levels')->insert([
            'name_ar' => 'مشتري مميز',
            'name_en' => 'special buyer',
            'name_fr' => 'acheteur spécial',
            'value_bayer_min' => 300,
        ]);
        DB::table('levels')->insert([
            'name_ar' => 'مشتري VIP',
            'name_en' => 'VIP buyer',
            'name_fr' => 'acheteur VIP',
        ]);
    }
}
