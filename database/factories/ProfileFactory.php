<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $fullname = explode(' ', $this->faker->name());
        $firstname = $fullname[0];
        $lastname = $fullname[1];
        return [
            'first_name' => $firstname,
            'last_name' => $lastname,
            'steps' => 3,
        ];
    }
}
