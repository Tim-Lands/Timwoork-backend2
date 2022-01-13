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
        /* -------------------------------------------------------------------------- */
        /*                             التصنيفات الرئيسية                             */
        /* -------------------------------------------------------------------------- */
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات مالية',
            'name_en'   => 'Financial Services',
            'icon'      => 'payments',
            'slug'      => 'financial-services'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'تقنية',
            'name_en'   => 'Technique',
            'icon'      => 'airplay',
            'slug'      => 'technique'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'استشارات ونصائح',
            'name_en'   => 'Advice',
            'icon'      => 'contact_support',
            'slug'      => 'Advice'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'خدمات ربات البيوت',
            'name_en'   => 'Housewives Services',
            'icon'      => 'baby_changing_station',
            'slug'      => 'housewives-services'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'كتابة و ترجمة ',
            'name_en'   => 'Writing and translating',
            'icon'      => 'keyboard_voice',
            'slug'      => 'writing-and-translating'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'صوتيات',
            'name_en'   => 'Audios',
            'icon'      => 'insert_chart_outlined',
            'slug'      => 'audios'
        ]);
        
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم عام',
            'name_en'   => 'General design',
            'icon'      => 'design_services',
            'slug'      => 'general-design'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'تصميم فيديو',
            'name_en'   => 'Design a video',
            'icon'      => 'ondemand_video',
            'slug'      => 'Design-a-video'
        ]);
        
        DB::table('categories')->insert([
            'name_ar'   => 'تدريب عن بعد',
            'name_en'   => 'remote training',
            'icon'      => 'cast_for_education',
            'slug'      => 'remote-training'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'تسويق الكتروني',
            'name_en'   => 'E-Marketing',
            'icon'      => 'monitor_heart',
            'slug'      => 'e-marketing'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'برمجة وتطوير',
            'name_en'   => 'Programming & Tech',
            'icon'      => 'code',
            'slug'      => 'programming-and-tech'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'اعمال',
            'name_en'   => 'Business',
            'icon'      => 'business',
            'slug'      => 'business'
        ]);
        /* -------------------------------------------------------------------------- */
        /*                              التصنيفات الفرعية                             */
        /* -------------------------------------------------------------------------- */
        /* ------------------------------- خدمات مالية ------------------------------ */
        DB::table('categories')->insert([
            'name_ar'   => 'سكريل',
            'name_en'   => 'Skrill',
            "parent_id" => 1,
            'slug'      => 'skrill'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'بايونير',
            'name_en'   => 'Payoneer',
            "parent_id" => 1,
            'slug'      => 'payoneer'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'بايبال',
            'name_en'   => 'PayPal',
            "parent_id" => 1,
            'slug'      => 'paypal'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'بايير',
            'name_en'   => 'Payeer',
            "parent_id" => 1,
            'slug'      => 'payeer'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'أرصدة جوالات',
            'name_en'   => 'Mobile credits',
            "parent_id" => 1,
            'slug'      => 'mobile-credits'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'شحن العاب',
            'name_en'   => 'Haulage Games',
            "parent_id" => 1,
            'slug'      => 'haulage-games'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 1,
            'slug'      => 'other'
        ]);

        /* ---------------------------------- تقنية --------------------------------- */
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات أندرويد',
            'name_en'   => 'Android Services',
            "parent_id" => 2,
            'slug'      => 'android-services'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'خدمات آيفون',
            'name_en'   => 'iPhone Services',
            "parent_id" => 2,
            'slug'      => 'iphone-services'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'حل مشاكل ويندوز',
            'name_en'   => 'Windows problem solving',
            "parent_id" => 2,
            'slug'      => 'windows-problem-solving'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'السيرفرات ولينكس',
            'name_en'   => 'Servers and linux',
            "parent_id" => 2,
            'slug'      => 'servers-and-linux'
        ]);
        
        DB::table('categories')->insert([
            'name_ar'   => 'ادخال بيانات',
            'name_en'   => 'Data entry',
            "parent_id" => 2,
            'slug'      => 'data-entry'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'اختبارات تجريبية',
            'name_en'   => 'Beta tests',
            "parent_id" => 2,
            'slug'      => 'beta-tests'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 2,
            'slug'      => 'other'
        ]);
        /* ----------------------------- استشارات ونصائح ---------------------------- */

        DB::table('categories')->insert([
            'name_ar'   => 'استشارات قانونية',
            'name_en'   => 'Legal advice',
            "parent_id" => 3,
            'slug'      => 'legal-advice'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات تسويقية',
            'name_en'   => 'Marketing consulting',
            "parent_id" => 3,
            'slug'      => 'marketing-consulting'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات مالية',
            'name_en'   => 'Financial advice',
            "parent_id" => 3,
            'slug'      => 'financial-advice'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'نصائح سياحة وسفر',
            'name_en'   => 'Tourism and travel tips',
            "parent_id" => 3,
            'slug'      => 'tourism-and-travel-tips'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات شخصية',
            'name_en'   => 'Personal advice',
            "parent_id" => 3,
            'slug'      => 'personal-advice'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'إستشارات لغوية',
            'name_en'   => 'Language advice',
            "parent_id" => 3,
            'slug'      => 'language-advice'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 3,
            'slug'      => 'other'
        ]);
        /* ---------------------------- خدمات ربات البيوت --------------------------- */
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات الطبخ',
            'name_en'   => 'Cooking services',
            "parent_id" => 4,
            'slug'      => 'cooking-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات الترجمة',
            'name_en'   => 'Translation services',
            "parent_id" => 4,
            'slug'      => 'translation-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات التصميم',
            'name_en'   => 'Design services',
            "parent_id" => 4,
            'slug'      => 'design-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات التطريز',
            'name_en'   => 'Embroidery services',
            "parent_id" => 4,
            'slug'      => 'embroidery-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'الكتابة الإبداعية',
            'name_en'   => 'Creative-writing',
            "parent_id" => 4,
            'slug'      => 'creative-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تفريغ نصوص',
            'name_en'   => 'Text dump',
            "parent_id" => 4,
            'slug'      => 'text-dump'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات تدقيق لغوي',
            'name_en'   => 'Proofreading services',
            "parent_id" => 4,
            'slug'      => 'proofreading-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات ترجمة',
            'name_en'   => 'Translation services',
            "parent_id" => 4,
            'slug'      => 'translation-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات تلخيص',
            'name_en'   => 'Summary services',
            "parent_id" => 4,
            'slug'      => 'summary-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة السيرة الذاتية',
            'name_en'   => 'CV writing',
            "parent_id" => 4,
            'slug'      => 'cv-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة النصوص الإعلانية',
            'name_en'   => 'Writing and texts',
            "parent_id" => 4,
            'slug'      => 'writing-and-texts'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة سيناريو',
            'name_en'   => 'Script writing',
            "parent_id" => 4,
            'slug'      => 'Script-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة محتوى متخصص',
            'name_en'   => 'Writing specialized content',
            "parent_id" => 4,
            'slug'      => 'writing-specialized-content'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة محتوى مواقع',
            'name_en'   => 'Website content writing',
            "parent_id" => 4,
            'slug'      => 'website-content-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة مقالات ومراجعات',
            'name_en'   => 'Writing articles and reviews',
            "parent_id" => 4,
            'slug'      => 'writing-articles-and-reviews'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 4,
            'slug'      => 'other'
        ]);

        /* ------------------------------ كتابة وترجمة ------------------------------ */
        
        DB::table('categories')->insert([
            'name_ar'   => 'الكتابة الإبداعية',
            'name_en'   => 'Creative-writing',
            "parent_id" => 5,
            'slug'      => 'creative-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تفريغ نصوص',
            'name_en'   => 'Text dump',
            "parent_id" => 5,
            'slug'      => 'text-dump'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات تدقيق لغوي',
            'name_en'   => 'Proofreading services',
            "parent_id" => 5,
            'slug'      => 'proofreading-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات ترجمة',
            'name_en'   => 'Translation services',
            "parent_id" => 5,
            'slug'      => 'translation-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات تلخيص',
            'name_en'   => 'Summary services',
            "parent_id" => 5,
            'slug'      => 'summary-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة السيرة الذاتية',
            'name_en'   => 'CV writing',
            "parent_id" => 5,
            'slug'      => 'cv-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة النصوص الإعلانية',
            'name_en'   => 'Writing and texts',
            "parent_id" => 5,
            'slug'      => 'writing-and-texts'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة سيناريو',
            'name_en'   => 'Script writing',
            "parent_id" => 5,
            'slug'      => 'Script-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة محتوى متخصص',
            'name_en'   => 'Writing specialized content',
            "parent_id" => 5,
            'slug'      => 'writing-specialized-content'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة محتوى مواقع',
            'name_en'   => 'Website content writing',
            "parent_id" => 5,
            'slug'      => 'website-content-writing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'كتابة مقالات ومراجعات',
            'name_en'   => 'Writing articles and reviews',
            "parent_id" => 5,
            'slug'      => 'writing-articles-and-reviews'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 5,
            'slug'      => 'other'
        ]);

        /* --------------------------------- صوتيات --------------------------------- */
        
        DB::table('categories')->insert([
            'name_ar'   => ' إنتاج كتب صوتية',
            'name_en'   => 'Audio book production',
            "parent_id" => 6,
            'slug'      => 'audio-book-production'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'المعالجة الصوتية',
            'name_en'   => 'Acoustic treatment',
            "parent_id" => 6,
            'slug'      => 'acoustic-treatment'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تحرير بودكاست',
            'name_en'   => 'Editing a podcast',
            "parent_id" => 6,
            'slug'      => 'editing-a-podcast'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعليق صوتي',
            'name_en'   => 'Voice comment',
            "parent_id" => 6,
            'slug'      => 'voice-comment'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 6,
            'slug'      => 'other'
        ]);

        /* -------------------------------- تصميم عام ------------------------------- */

        DB::table('categories')->insert([
            'name_ar'   => 'أغلفة كتب ومجلات',
            'name_en'   => 'Book and magazine covers',
            "parent_id" => 7,
            'slug'      => 'book-and-magazine-covers'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم بانرات إعلانية',
            'name_en'   => 'Advertising banners design',
            "parent_id" => 7,
            'slug'      => 'advertising-banners-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم بطاقات أعمال',
            'name_en'   => 'Business card design',
            "parent_id" => 7,
            'slug'      => 'business-card-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم تيشرتات',
            'name_en'   => 'T-shirt design',
            "parent_id" => 7,
            'slug'      => 't-shirt-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم شعار',
            'name_en'   => 'Designing a logo',
            "parent_id" => 7,
            'slug'      => 'designing a logo'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم عروض تقديمية',
            'name_en'   => 'Presentation Design',
            "parent_id" => 7,
            'slug'      => 'presentation-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم معماري وديكور',
            'name_en'   => 'Architectural design and decoration',
            "parent_id" => 7,
            'slug'      => 'architectural-design-and-decoration'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات تعديل الصور',
            'name_en'   => 'Photo editing services',
            "parent_id" => 7,
            'slug'      => 'photo editing services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'رسوم كارتونية',
            'name_en'   => 'Cartoons',
            "parent_id" => 7,
            'slug'      => 'cartoons'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'فلاير وبرشور ورول اب',
            'name_en'   => 'Flyer brochure and roll up',
            "parent_id" => 7,
            'slug'      => 'flyer-brochure-and-roll-up'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 7,
            'slug'      => 'other'
        ]);
        /* ------------------------------- تصميم فيديو ------------------------------ */

        DB::table('categories')->insert([
            'name_ar'   => 'تصميم صور متحركة',
            'name_en'   => 'Animation design',
            "parent_id" => 8,
            'slug'      => 'animation-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تصميم مقدمات فيديو',
            'name_en'   => 'Design video intros',
            "parent_id" => 8,
            'slug'      => 'design-video-intros'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'موشن جرافيك',
            'name_en'   => 'Motion graphics',
            "parent_id" => 8,
            'slug'      => 'motion-graphics'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'مونتاج فيديو',
            'name_en'   => 'Video montage',
            "parent_id" => 8,
            'slug'      => 'video-montage'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'وايت بورد',
            'name_en'   => 'Whiteboard',
            "parent_id" => 8,
            'slug'      => 'whiteboard'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 8,
            'slug'      => 'other'
        ]);
        /* ------------------------------- تدريب عن بعد ------------------------------ */
        
        DB::table('categories')->insert([
            'name_ar'   => 'تدريب عن بعد',
            'name_en'   => ' Remote-training',
            "parent_id" => 9,
            'slug'      => ' remote training'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات شخصية',
            'name_en'   => 'Personal advice',
            "parent_id" => 9,
            'slug'      => 'personal-advice'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'الصحة واللياقة البدنية',
            'name_en'   => 'Health and fitness',
            "parent_id" => 9,
            'slug'      => 'health-and-fitness'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعلم البرمجة',
            'name_en'   => 'Learn programming',
            "parent_id" => 9,
            'slug'      => 'learn-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => ' تعلم التسويق الالكتروني',
            'name_en'   => 'Learn email marketing',
            "parent_id" => 9,
            'slug'      => 'learn-email-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعلم التصميم',
            'name_en'   => 'Learn design',
            "parent_id" => 9,
            'slug'      => 'learn-design'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعلم اللغات',
            'name_en'   => 'Learning languages',
            "parent_id" => 9,
            'slug'      => 'learning-languages'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعلم اللغة الإنجليزية',
            'name_en'   => 'Learn English',
            "parent_id" => 9,
            'slug'      => 'learn-English'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تعلم اللغة الفرنسية',
            'name_en'   => 'Learn the French Language',
            "parent_id" => 9,
            'slug'      => 'learn-the-french-language'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'فنون وحرف',
            'name_en'   => 'Arts and crafts',
            "parent_id" => 9,
            'slug'      => 'arts-and-crafts'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'مساعدة بحل الواجبات',
            'name_en'   => 'Help with homework',
            "parent_id" => 9,
            'slug'      => 'help-with-homework'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'نصائح سياحة وسفر',
            'name_en'   => 'Tourism and travel tips',
            "parent_id" => 9,
            'slug'      => 'tourism-and-travel-tips'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 9,
            'slug'      => 'other'
        ]);
        /* ----------------------------- تسويق الكتروني ----------------------------- */
            
        DB::table('categories')->insert([
            'name_ar'   => 'إعلانات المواقع',
            'name_en'   => 'Website ads',
            "parent_id" => 10,
            'slug'      => 'website-ads'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات تسويقية',
            'name_en'   => 'Marketing Consulting',
            "parent_id" => 10,
            'slug'      => 'marketing-consulting'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'الإعلانات والتسويق على الجوال',
            'name_en'   => 'Mobile Advertising and Marketing',
            "parent_id" => 10,
            'slug'      => 'mobile-advertising-and-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق بالمحتوى',
            'name_en'   => 'Content Marketing',
            "parent_id" => 10,
            'slug'      => 'content-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق عبر البريد الإلكتروني',
            'name_en'   => 'Email Marketing',
            "parent_id" => 10,
            'slug'      => 'email-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق عبر انستقرام',
            'name_en'   => 'Instagram Marketing',
            "parent_id" => 10,
            'slug'      => 'instagram-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق عبر تيك توك',
            'name_en'   => 'Marketing through Tik Tok',
            "parent_id" => 10,
            'slug'      => 'marketing-through-tik-tok'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق عبر محركات البحث SEM',
            'name_en'   => 'Search Engine Marketing SEM',
            "parent_id" => 10,
            'slug'      => 'search-engine-marketing-sem'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق على تويتر',
            'name_en'   => 'Twitter Marketing',
            "parent_id" => 10,
            'slug'      => 'twitter-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق على سناب شات',
            'name_en'   => 'Marketing on Snapchat',
            "parent_id" => 10,
            'slug'      => 'marketing-on-snapchat'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق على فيسبوك',
            'name_en'   => 'Facebook Marketing',
            "parent_id" => 10,
            'slug'      => 'facebook-marketing'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'التسويق على يوتيوب',
            'name_en'   => 'other',
            "parent_id" => 10,
            'slug'      => 'other'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'تحليلات المواقع',
            'name_en'   => 'Website Analytics',
            "parent_id" => 10,
            'slug'      => 'website-analytics'
        ]);

        DB::table('categories')->insert([
            'name_ar'   => 'خدمات SEO',
            'name_en'   => 'SEO Services',
            "parent_id" => 10,
            'slug'      => 'seo-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 10,
            'slug'      => 'other'
        ]);

        /* ------------------------------ برمجة وتطوير ------------------------------ */
 
        DB::table('categories')->insert([
            'name_ar'   => 'أنظمة ادارة المحتوى',
            'name_en'   => 'Content management systems',
            "parent_id" => 11,
            'slug'      => 'content-management-systems'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اختبارات تجريبية',
            'name_en'   => 'Beta tests',
            "parent_id" => 11,
            'slug'      => 'beta-tests'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'السيرفرات ولينكس',
            'name_en'   => 'Servers and linux',
            "parent_id" => 11,
            'slug'      => 'servers-and-linux'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'برمجة CSS و HTML',
            'name_en'   => 'CSS and HTML programming',
            "parent_id" => 11,
            'slug'      => 'css-and-html-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'برمجة Java و .NET',
            'name_en'   => 'Java and .NET programming',
            "parent_id" => 11,
            'slug'      => 'java-and-.NET-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'برمجة PHP',
            'name_en'   => 'PHP Programming',
            "parent_id" => 11,
            'slug'      => 'php-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'برمجة بايثون',
            'name_en'   => 'Python programming',
            "parent_id" => 11,
            'slug'      => 'python-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'برمجة تطبيقات الجوال',
            'name_en'   => 'Mobile app programming',
            "parent_id" => 11,
            'slug'      => 'mobile-app-programming'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تطبيقات سطح المكتب',
            'name_en'   => 'Desktop apps',
            "parent_id" => 11,
            'slug'      => 'desktop-apps'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تطوير متاجر إلكترونية',
            'name_en'   => 'E-commerce store development',
            "parent_id" => 11,
            'slug'      => 'e-commerce-store-development'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات مدونات بلوجر',
            'name_en'   => 'Blogger blogging services',
            "parent_id" => 11,
            'slug'      => 'blogger-blogging-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات ووردبريس',
            'name_en'   => 'WordPress Services',
            "parent_id" => 11,
            'slug'      => 'wordPress-services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'دعم فني تقني',
            'name_en'   => 'Technical Support',
            "parent_id" => 11,
            'slug'      => 'technical-support'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 11,
            'slug'      => 'other'
        ]);

        /* ---------------------------------- اعمال --------------------------------- */
        
        DB::table('categories')->insert([
            'name_ar'   => 'ادخال بيانات',
            'name_en'   => 'Data entry',
            "parent_id" => 12,
            'slug'      => 'data-entry'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'استشارات أعمال',
            'name_en'   => 'Business Consulting',
            "parent_id" => 12,
            'slug'      => 'business-consulting'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'تجارة الكترونية',
            'name_en'   => 'Electronic trade',
            "parent_id" => 12,
            'slug'      => 'electronic-trade'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'خدمات قانونية',
            'name_en'   => 'Legal services',
            "parent_id" => 12,
            'slug'      => 'legal services'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'دراسات وأبحاث',
            'name_en'   => 'Studies and research',
            "parent_id" => 12,
            'slug'      => 'studies-and-research'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'مساعد افتراضي',
            'name_en'   => 'Virtual-assistant',
            "parent_id" => 12,
            'slug'      => 'virtual-assistant'
        ]);
        DB::table('categories')->insert([
            'name_ar'   => 'اخرى',
            'name_en'   => 'other',
            "parent_id" => 12,
            'slug'      => 'other'
        ]);
    }
    /* -------------------------------------------------------------------------- */
}
