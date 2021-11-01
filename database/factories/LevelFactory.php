<?php

namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Level::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name_ar' =>  $this->faker->name,
            'number_developments' => rand(1, 20),
            'price_developments' => rand(5, 100),
            'number_sales' => rand(20, 120),
            'value_bayer' => rand(100, 1000),
        ];
    }
}
