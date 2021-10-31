<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name_en = Str::slug($this->faker->name);
        $name_ar = \Faker\Factory::create('ar_JO')->name;
        return [
            'name_ar'        => $name_ar,
            'name_en'        => $this->faker->name,
            'name_fr'        => $this->faker->name,
            'slug'           => $name_en,
            'description_ar' => $this->faker->paragraph(1),
            'description_en' => $this->faker->paragraph(1),
            'description_fr' => $this->faker->paragraph(1),
            'icon'           => $this->faker->word(),

        ];
    }
}
