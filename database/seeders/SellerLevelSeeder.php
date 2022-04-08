<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellerLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('seller_levels')->insert([
            'name_ar' => 'بائع جديد',
            'name_en' => 'new seller',
            'name_fr' => 'nouveau vendeur',
            'number_of_sales' => 0,
            'products_number_max' => 6,
            'number_developments_max' => 5,
            'price_development_max' => 100,
        ]);

        DB::table('seller_levels')->insert([
            'name_ar' => 'بائع نشيط',
            'name_en' => 'active seller',
            'name_fr' => 'vendeur actif',
            'number_of_sales' => 10,
            'products_number_max' => 12,
            'number_developments_max' => 8,
            'price_development_max' => 150,
        ]);

        DB::table('seller_levels')->insert([
            'name_ar' => 'بائع مميز',
            'name_en' => 'featured seller',
            'name_fr' => 'vendeur vedette',
            'number_of_sales' => 50,
            'products_number_max' => 25,
            'number_developments_max' => 10,
            'price_development_max' => 200,
        ]);

        DB::table('seller_levels')->insert([
            'name_ar' => 'بائع موثوق',
            'name_en' => 'trusted seller',
            'name_fr' => 'vendeur de confiance',
            'reliability' => true,
            'number_of_sales' => 0,
            'products_number_max' => 0,
            'number_developments_max' => 12,
            'price_development_max' => 250,
        ]);
    }
}
