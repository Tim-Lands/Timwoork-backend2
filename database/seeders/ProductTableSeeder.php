<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\Galary;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::factory()->times(15)->create()
            ->each(function ($product) {
                $product->product_tag()->saveMany(Tag::factory()->times(4)->make());
            })
            ->each(function ($product) {
                $product->developments()->saveMany(Development::factory()->times(3)->make());
            })
            ->each(function ($product) {
                $product->galaries()->saveMany(Galary::factory()->times(2)->make());
            });
    }
}
