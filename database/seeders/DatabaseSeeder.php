<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            LevelSeeder::class,
            BadgeSeeder::class,
            SellerBadgeSeeder::class,
            SellerLevelSeeder::class,
            SkillTableSeeder::class,
            LanguageTableSeeder::class,
            CategoryTableSeeder::class,
            TagTableSeeder::class,
            CountryTableSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            ProductTableSeeder::class,
        ]);
    }
}
