<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Profile;
use App\Models\ProfileSeller;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langs = Language::all();
        $cats = Category::all();
        $skills = Skill::all();
        User::factory()->times(4)->create()
            ->each(function ($u) {
                $u->profile()->save(
                    Profile::factory()->make()
                );
            })
            ->each(function ($p) {
                $p->profile->profile_seller()->save(ProfileSeller::factory()->make());
            });

        ProfileSeller::all()->each(function ($p) use ($skills, $langs, $cats) {
            $p->languages()->attach($langs->random(rand(1, 3))->pluck('id')->toArray(), ["level" => rand(5, 10)]);
            $p->skills()->attach($skills->random(rand(1, 10))->pluck('id')->toArray(), ["level" => rand(5, 10)]);
            $p->professions()->attach($cats->random(rand(1, 12))->pluck('id')->toArray());
        });
    }
}
