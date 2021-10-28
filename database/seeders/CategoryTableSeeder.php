<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // انشاء حقول عشوائية مع العلاقة
        Category::factory()->times(9)->create()->each(function ($category) {
            $category->subcategories()->saveMany(Category::factory()->times(3)->make());
        });
    }
}
