<?php

namespace Database\Seeders;

use App\Models\CartItem;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class translateCartItemsSeeder extends Seeder
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
        $cart_items = CartItem::all();
        foreach ($cart_items as $cart_item) {

            $cart_item->product_title_ar = $cart_item->product_title;
            $cart_item->product_title_en = $tr->translate($cart_item->product_title);
            $cart_item->save();
        }
        $tr->setTarget('fr');
        foreach ($cart_items as $cart_item) {
            $cart_item->product_title_fr = $tr->translate($cart_item->product_title);
            $cart_item->save();
        }
    }
}
