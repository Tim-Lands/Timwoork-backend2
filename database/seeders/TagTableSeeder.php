<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* -------------------------------------------------------------------------- */
        /*                                   الوسوم                                   */
        /* -------------------------------------------------------------------------- */
        
        DB::table('tags')->insert([
            'name'   => 'HTML',
        ]);
        DB::table('tags')->insert([
            'name'   => 'CSS',
        ]);
        DB::table('tags')->insert([
            'name'   => 'JAVA',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Marketing',
        ]);
        DB::table('tags')->insert([
            'name'   => 'PHP',
        ]);
        DB::table('tags')->insert([
            'name'   => 'LARAVEL',
        ]);
        DB::table('tags')->insert([
            'name'   => 'ASP.NET',
        ]);
        DB::table('tags')->insert([
            'name'   => 'JAVASCRIPT',
        ]);
        DB::table('tags')->insert([
            'name'   => 'RUBY',
        ]);
        DB::table('tags')->insert([
            'name'   => 'PYTHON',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Programming',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Mysql',
        ]);
        DB::table('tags')->insert([
            'name'   => 'SQL',
        ]);
        DB::table('tags')->insert([
            'name'   => 'NOSQL',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Andoid',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Linux',
        ]);
        DB::table('tags')->insert([
            'name'   => 'Docker',
        ]);
        DB::table('tags')->insert([
            'name'   => 'bash',
        ]);
        DB::table('tags')->insert([
            'name'   => 'C++',
        ]);
        DB::table('tags')->insert([
            'name'   => 'wordbress',
        ]);
    }
}
