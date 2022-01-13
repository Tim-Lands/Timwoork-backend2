<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->text(30);
        $name_images = [1 => "Tarek",2 => "Abdallah",3 => "Roqia",4 => "Hamid"];
        $key = array_rand($name_images);
        return [
            'title'                 => $title,
            'slug'                  => Str::slug($title),
            'content'               => $this->faker->paragraph(1),
            'price'                 => $this->faker->numberbetween(5, 100),
            // 'duration'              => $this->faker->dateTimeBetween('now', '+05 days'),
            'duration'              => $this->faker->numberBetween(1, 60),
            'count_buying'              => $this->faker->numberBetween(0, 80),
            'current_step'          => 5,
            'is_active'             => 1,
            'is_completed'          => 1,
            'is_draft'              => 0,
            'thumbnail'             => $this->faker->image(storage_path('app/products/thumbnails'), 640, 480, null, null, null, $name_images[$key], false),
            'buyer_instruct'        => $this->faker->paragraph(1),
            'profile_seller_id'     => $this->faker->numberBetween(1, 4),
            // اختر اي تصنيف فرعي او ضع اعداد عشوائية للتصنيفات المتواجدة
            'category_id'           => rand(13, 120), // $this->faker->numberBetween(n,m);
            'ratings_avg'         => rand(0, 100),
            'ratings_count'         => rand(0, 5)
        ];
    }
}
