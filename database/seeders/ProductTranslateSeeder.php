<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ProductTranslateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $tr = new GoogleTranslate();
        $tr->setSource('ar');
        $tr->setTarget('en');
        $products = Product::all();
        foreach($products as $product){

            $product->title_ar = $product->title;
            $product->content_ar = $product->content;
            $product->buyer_instruct_ar = $product->buyer_instruct;
            if(!is_null($product->title_ar))
            $product->title_en =  $tr->translate($product->title);
            if(!is_null($product->content_ar))
            $product->content_en = $tr->translate($product->content);
            if(!is_null($product->buyer_instruct_ar))
            $product->buyer_instruct_en =$tr->translate($product->buyer_instruct);
            $product->save();
        }
        $tr->setTarget('fr');
        foreach($products as $product){
            if(!is_null($product->title_ar))
            $product->title_fr =  $tr->translate($product->title_ar);
            if(!is_null($product->content_ar))
            $product->content_fr = $tr->translate($product->content_ar);
            if(!is_null($product->buyer_instruct_ar))
            $product->buyer_instruct_fr =$tr->translate($product->buyer_instruct_ar);
            $product->save();
        }
    }
}
