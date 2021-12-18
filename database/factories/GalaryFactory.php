<?php

namespace Database\Factories;

use App\Models\Galary;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Galary::class;

    public function definition()
    {
        $image = $this->faker->image(storage_path('app/products/galaries-images'), 640, 480, null, null, null, 'tarek', false);
        return [
            'path' => $image,
            'full_path' => $image,
            'size' => $this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0.1, $max = 2) . ' mb',
            'url_video' => 'https://www.youtube.com/watch?v=B17oiTBZCvc',
            'type_file' => 'image',
            'mime_type' => 'png'
        ];
    }
}
