<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

use function PHPUnit\Framework\isNull;

class translateNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $tr = new GoogleTranslate();
            $tr->setSource('ar');
            $tr->setTarget('en');
            $notifications = DB::table('notifications')->get();
            foreach ($notifications as $notification) {
                $data = json_decode($notification->data);
                if(isset($data->title)){
                    $title_ar = $data->title;
                    $title_en = $tr->translate($title_ar);
                    $data->title_ar = $title_ar;
                    $data->title_en = $title_en;
                }
                if (isset($data->content) && isset($data->content->slug)) {
                    $content_slug_ar = $data->content->slug;
                    $content_slug_en = $tr->translate($data->content->slug);
                    $data->content->slug_ar = $content_slug_ar;
                    $data->content->slug_en = $content_slug_en;
                }
                if (isset($data->user_sender) && !isset($data->user_sender->full_name)) {
                    $full_name_ar = $data->user_sender->full_name;
                    $full_name_en = $tr->translate($data->user_sender->full_name_en);
                    $data->user_sender->full_name_ar = $full_name_ar;
                    $data->user_sender->full_name_en = $full_name_en;
                }
                if (isset($data->content) && isset($data->content->cause)) {
                    $content_cause_ar = $data->content->cause;
                    $content_cause_en = $tr->translate($data->content->cause);
                    $data->content->cause_ar = $content_cause_ar;
                    $data->content->cause_en = $content_cause_en;
                }
                if (isset($data->content) && isset($data->content->title)) {
                    $content_title_ar = $data->content->title;
                    $content_title_en = $tr->translate($data->content->title);
                    $data->content->title_ar = $content_title_ar;
                    $data->content->title_en = $content_title_en;
                }
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
            }
            $tr->setTarget('fr');
            foreach ($notifications as $notification) {
                $data = json_decode($notification->data);
                if(isset($data->title)){
                    $title_ar = $data->title;
                    $title_fr = $tr->translate($title_ar);
                    $data->title_ar = $title_ar;
                    $data->title_fr = $title_fr;
                }
                if (isset($data->user_sender) && isset($data->user_sender->full_name)) {
                    $full_name_fr = $tr->translate($data->user_sender->full_name);
                    $data->user_sender->full_name_fr = $full_name_fr;
                }
                if (isset($data->content) && isset($data->content->slug)) {
                    $content_slug_ar = $data->content->slug;
                    $content_slug_fr = $tr->translate($data->content->slug);
                    $data->content->slug_ar = $content_slug_ar;
                    $data->content->slug_en = $content_slug_fr;
                }
                if (isset($data->content) && isset($data->content->cause)) {
                    $content_cause_fr = $tr->translate($data->content->cause);
                    $data->content->cause_fr = $content_cause_fr;
                }

                if (isset($data->content) && isset($data->content->title)) {
                    $content_title_ar = $data->content->title;
                    $content_title_fr = $tr->translate($data->content->title);
                    $data->content->title_ar = $content_title_ar;
                    $data->content->title_fr = $content_title_fr;
                }
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['data' => json_encode($data)]);
            }
        } catch (Exception $e) {
            echo  $e;
        }
    }
}
