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
        return [
            'title'             => $title,
            'slug'              => Str::slug($title),
            'content'           => $this->faker->paragraph(3),
            'price'             => $this->faker->numberbetween(5, 10),
            'duration'          => $this->faker->dateTimeBetween('now', '+05 days'),
            // 'some_develop'      => $this->faker->text(200),
            'buyer_instruct'    => $this->faker->paragraph(3),
            'profile_seller_id' => $this->faker->numberBetween(1, 3),
            // اختر اي تصنيف فرعي او ضع اعداد عشوائية للتصنيفات المتواجدة
            'category_id'       => rand(10, 19) // $this->faker->numberBetween(n,m);
        ];
    }
}
