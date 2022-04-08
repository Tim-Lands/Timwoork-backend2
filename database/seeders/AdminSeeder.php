<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->insert([
            'username' => 'admin',
            'first_name' => 'super',
            'last_name' => 'admin',
            'email' => 'admin@timlands.com',
            'password' => bcrypt('password'),
        ]);
    }
}
