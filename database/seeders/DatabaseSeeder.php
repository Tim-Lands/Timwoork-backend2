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
            AdminSeeder::class,
            CategoryTableSeeder::class,
            ProductTableSeeder::class,
            CountryTableSeeder::class,
            TagTableSeeder::class,
            SkillTableSeeder::class,
            LanguageTableSeeder::class,
        ]);
    }
}
