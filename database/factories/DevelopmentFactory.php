<?php

namespace Database\Factories;

use App\Models\Development;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevelopmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Development::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'                 => $this->faker->text(30),
            'price'                 => $this->faker->numberbetween(5, 10),
            'duration'              => $this->faker->numberBetween(1, 10),
        ];
    }
}
