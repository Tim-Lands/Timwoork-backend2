<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WiseCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            ['ar_name' => 'الأرجنتين'],
            ['ar_name' =>'أستراليا'],
            ['ar_name' =>'النمسا'],
            ['ar_name' =>'بنغلاديش'],
            ['ar_name' =>'بلجيكا'],
            ['ar_name' =>'البرازيل'],
            ['ar_name' =>'بلغاريا'],
            ['ar_name' =>'كندا'],
            ['ar_name' =>'تشيلي'],
            ['ar_name' => 'الصين'],
            ['ar_name' => 'كرواتيا'],
            ['ar_name' => 'قبرص'],
            ['ar_name' => 'جمهورية التشيك'],
            ['ar_name' => 'الدنمارك'],
            ['ar_name' => 'مصر'],
            ['ar_name' => 'إستونيا'],
            ['ar_name' => 'فنلندا'],
            ['ar_name' => 'فرنسا'],
            ['ar_name' => 'جورجيا'],
            ['ar_name' => 'ألمانيا'],
            ['ar_name' => 'غانا'],
            ['ar_name' => 'اليونان'],
            ['ar_name' => 'هونج كونج'],
            ['ar_name' => 'هنغاريا'],
            ['ar_name' => 'الهند'],
            ['ar_name' => 'إندونيسيا'],
            ['ar_name' => 'أيرلندا'],
            ['ar_name' => 'إيطاليا'],
            ['ar_name' => 'كينيا'],
            ['ar_name' => 'لاتفيا'],
            ['ar_name' => 'لوكسمبورغ'],
            ['ar_name' => 'ماليزيا'],
            ['ar_name' => 'مالطا'],
            ['ar_name' => 'المكسيك'],
            ['ar_name' =>'موناكو'],
            ['ar_name' =>'المغرب'],
            ['ar_name' =>'هولندا'],
            ['ar_name' =>'نيوزيلاندا'],
            ['ar_name' =>'نيجيريا'],
            ['ar_name' =>'النرويج'],
            ['ar_name' =>'باكستان'],
            ['ar_name' =>'فيلبيني'],
            ['ar_name' =>'بولندا'],
            ['ar_name' =>'البرتغال'],
            ['ar_name' =>'رومانيا'],
            ['ar_name' =>'الاتحاد الروسي'],
            ['ar_name' =>'سان مارينو'],
            ['ar_name' =>'سنغافورة'],
            ['ar_name' =>'سلوفاكيا'],
            ['ar_name' =>'سلوفينيا'],
            ['ar_name' =>'جنوب أفريقيا'],
            ['ar_name' =>'إسبانيا'],
            ['ar_name' =>'سيريلانكا'],
            ['ar_name' =>'السويد'],
            ['ar_name' =>'سويسرا'],
            ['ar_name' =>'تنزانيا'],
            ['ar_name' =>'تايلاند'],
            ['ar_name' =>'تركيا'],
            ['ar_name' =>'أوغندا'],
            ['ar_name' =>'أوكرانيا'],
            ['ar_name' =>'الإمارات العربية المتحدة'],
            ['ar_name' =>'المملكة المتحدة'],
            ['ar_name' =>'الولايات المتحدة الأمريكية'],
        ];
        DB::table('wise_countries')->insert($countries);
    }
}
