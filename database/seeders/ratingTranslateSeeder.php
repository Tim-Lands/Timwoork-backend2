<?php

namespace Database\Seeders;

use App\Models\Rating;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ratingTranslateSeeder extends Seeder
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
        $ratings = Rating::all();
        foreach($ratings as $rating){

            $rating->comment_ar = $rating->comment;
            $rating->comment_en = $tr->translate($rating->comment);
            $rating->save();
        }
        $tr->setTarget('fr');
        foreach($ratings as $rating){
            $rating->comment_fr = $tr->translate($rating->comment);
            $rating->save();
        }
    }
}
