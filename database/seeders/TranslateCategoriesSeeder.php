<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tr = new GoogleTranslate();
        $tr->setSource('ar');
        $tr->setTarget('en');
        $categories = Category::all();
        foreach ($categories as $categorie) {
            if(is_null($categorie->name_ar))
                continue;

            $categorie->name_en = $tr->translate($categorie->name_ar);
            $categorie->save();
        }
        $tr->setTarget('fr');
        foreach ($categories as $categorie) {
            if(is_null($categorie->name_ar))
                continue;
            $categorie->name_fr = $tr->translate($categorie->name_ar);
            $categorie->save();
        }
        }
}
