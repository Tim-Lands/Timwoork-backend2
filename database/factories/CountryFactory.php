<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name_country = $this->faker->unique()->country();
        return [
            'name_ar'        => $name_country,
            'code_phone'     => $this->faker->numerify('+' . $this->faker->randomNumber(3, false)),
            'flag'           => $this->faker->name
        ];
    }
}
