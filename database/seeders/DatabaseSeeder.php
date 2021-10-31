<?php

namespace Database\Seeders;

use Database\Factories\CategoryFactory;
use Database\Factories\ProdutFactory;
use Database\Factories\SubCategoryFactory;
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
        ]);
    }
}
