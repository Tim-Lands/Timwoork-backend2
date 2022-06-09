<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class createWalletInProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = Profile::doesntHave('wallet')->get();
        $data->each(function ($collection) {
            $collection->wallet()->create([]);
        });
    }
}
