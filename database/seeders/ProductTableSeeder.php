<?php

namespace Database\Seeders;

use App\Models\Development;
use App\Models\Galary;
use App\Models\Product;
use App\Models\Tag;
use App\Models\Video;
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
        $thumbnails = glob(storage_path('app/products/thumbnails/*.*'));
        $images = glob(storage_path('app/products/galaries-images/*.*'));
        foreach ($thumbnails as $thumbnail) {
            unlink($thumbnail);
        }
        foreach ($images as $image) {
            unlink($image);
        }
        
        Product::factory()->times(15)->create()
            ->each(function ($product) {
                $product->developments()->saveMany(Development::factory()->times(3)->make());
            })
            ->each(function ($product) {
                $product->galaries()->saveMany(Galary::factory()->times(2)->make());
            })
            ->each(function ($product) {
                $product->video()->save(Video::factory()->make());
            });
        // factory tags
        $tags = Tag::all();
        // Populate the pivot table
        Product::all()->each(function ($product) use ($tags) {
            $product->product_tag()->attach(
                $tags->random(rand(1, 20))->pluck('id')->toArray()
            );
        });
    }
}
