<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            /*LevelSeeder::class,
            BadgeSeeder::class,
            SellerBadgeSeeder::class,
            SellerLevelSeeder::class,
            SkillTableSeeder::class,
            LanguageTableSeeder::class,
            CategoryTableSeeder::class,*/
            //TagTableSeeder::class,
            //CountryTableSeeder::class,
            //WiseCountrySeeder::class,
            //AdminSeeder::class,
            //UserSeeder::class,
            //ProductTableSeeder::class,
            //TypePaymentSeeder::class,
            // CurrencySeeder::class,
            // CountrySeeder::class,
            // ProfileCurrencySeeder::class,
            // CountryPhoneCodeSeeder::class,
            // createWalletInProfilesSeeder::class
            //CountriesLangSeederClass::class
            //ProfileLangSeederClass::class
            ProductTranslateSeeder::class
            //CountryTranslateSeeder::class
            //ratingTranslateSeeder::class
            //TranslateDevelopmentSeeder::class
            //translate_items::class
            //TranslateBanSeeder::class
            //translateNotificationSeeder::class
            //translateCartItemsSeeder::class
            //translateConversationSeeder::class
            //TranslateWalletActivitiesSeeder::class
            //TranslateProfileBio::class
            //TranslateCategoriesSeeder::class
        ]);
    }
}
