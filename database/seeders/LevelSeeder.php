<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Level::factory()->count(4)->state(new Sequence(
            ['type' => 0],
            ['type' => 1],
        ))->create();
    }
}
