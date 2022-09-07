<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class translate_items extends Seeder
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
        $items = Item::all();
        foreach($items as $item){

            $item->title_ar = $item->title;
            $item->title_en = $tr->translate($item->title);
            $item->save();
        }
        $tr->setTarget('fr');
        foreach($items as $item){
            $item->title_fr = $tr->translate($item->title);
            $item->save();
        }
    }
}
