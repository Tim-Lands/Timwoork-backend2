<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            'name_ar'   => 'نمط الحياة',
            'name_en'   => 'LifeStyle',
            'icon'      => 'support',
            'slug'      => 'life-style'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'صوتيات',
            'name_en'   => 'Records',
            'icon'      => 'settings_voice',
            'slug'      => 'records'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'إدارة أعمال',
            'name_en'   => 'Business',
            'icon'      => 'business',
            'slug'      => 'business'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'كتابة وترجمة',
            'name_en'   => 'Writing & Translation',
            'icon'      => 'rate_review',
            'slug'      => 'writing-and-translation'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'التصميم الغرافيكي',
            'name_en'   => 'Graphic Design',
            'icon'      => 'view_in_ar',
            'slug'      => 'graphic-design'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'التسويق الرقمي',
            'name_en'   => 'Digital Marketing',
            'icon'      => 'insert_chart_outlined',
            'slug'      => 'digital-marketing'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'برمجة وتطوير',
            'name_en'   => 'Programming & Tech',
            'icon'      => 'code',
            'slug'      => 'programming-and-tech'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'الحركات وتركيب الفيديو',
            'name_en'   => 'Video & Animation',
            'icon'      => 'video_camera_back',
            'slug'      => 'video-and-animation'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'تطوير الويب',
            'name_en'   => 'web development',
            'parent_id'      => 7,
            'slug'      => 'web-development'
        ]);
    }
}
