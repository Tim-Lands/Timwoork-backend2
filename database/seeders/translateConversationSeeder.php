<?php

namespace Database\Seeders;

use App\Models\Conversation;
use Illuminate\Database\Seeder;
use Stichoza\GoogleTranslate\GoogleTranslate;

class translateConversationSeeder extends Seeder
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
        $conversations = Conversation::all();
        foreach ($conversations as $conversation) {

            $conversation->title_ar = $conversation->title;
            $conversation->title_en = $tr->translate($conversation->title);
            $conversation->save();
        }
        $tr->setTarget('fr');
        foreach ($conversations as $conversation) {
            $conversation->title_fr = $tr->translate($conversation->title);
            $conversation->save();
        }    }
}
