<?php

namespace Database\Factories;

use App\Models\ProfileSeller;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileSellerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProfileSeller::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'portfolio' => $this->faker->url(),
            'bio' => $this->faker->text(),
            'steps' => 3,
        ];
    }
}
