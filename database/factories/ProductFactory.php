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
        $current_step = $this->faker->numberBetween(0, 5);
        $completed = $current_step == 5 ? 1 : 0;
        $title = $this->faker->text(30);
        return [
            'title'                 => $title,
            'slug'                  => Str::slug($title),
            'content'               => $this->faker->paragraph(1),
            'price'                 => $this->faker->numberbetween(5, 10),
            // 'duration'              => $this->faker->dateTimeBetween('now', '+05 days'),
            'duration'              => $this->faker->numberBetween(1, 10),
            'current_step'          => $current_step,
            'is_active'             => $this->faker->numberBetween(0, 1),
            'is_completed'          => $completed,
            'is_draft'              => $completed,
            // 'thumbnail'             => $this->faker->image('public/storage/images-thumb', 640, 480, null, false),
            'buyer_instruct'        => $this->faker->paragraph(1),
            'profile_seller_id'     => $this->faker->numberBetween(1, 3),
            // اختر اي تصنيف فرعي او ضع اعداد عشوائية للتصنيفات المتواجدة
            'category_id'           => rand(10, 27) // $this->faker->numberBetween(n,m);
        ];
    }
}
