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
            'title'             => $this->faker->text(30),
            'slug'              => Str::slug($title),
            'content'           => $this->faker->text(200),
            'price'             => $this->faker->numberbetween(1, 5),
            'duration'          => $this->faker->dateTimeBetween('now', '+05 days'),
            // 'some_develop'      => $this->faker->text(200),
            'buyer_instruct'    => $this->faker->text(200),
            'profile_seller_id' => $this->faker->numberBetween(1, 3),
            // اختر اي تصنيف فرعي او ضع اعداد عشوائية للتصنيفات المتواجدة
            'category_id'       => '7' // $this->faker->numberBetween(n,m);
        ];
    }
}
