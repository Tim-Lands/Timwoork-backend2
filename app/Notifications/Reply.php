<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Reply extends Notification
{
    use Queueable;
    public $user;
    public $id;
    public $title;
    public $title_ar;
    public $title_en;
    public $title_fr;
    public $rating_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $id, $title, $title_ar, $title_en, $title_fr, $rating_id)
    {
        $this->user       = $user;
        $this->id         = $id;
        $this->title      = $title;
        $this->title_ar      = $title_ar;
        $this->title_en      = $title_en;
        $this->title_fr      = $title_fr;

        $this->rating_id  = $rating_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [  'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), config('mail.from.ar_name'))
            ->subject('الرد على التعليق')
            ->view('emails.products.reply', [
                'type' => "rating",
                'to' => "buyer",
                'title' =>  " قام " . Auth::user()->profile->full_name . " بالرد على تعليقك ",
                'user_sender' => [
                    'full_name' => Auth::user()->profile->full_name,
                    'username' => Auth::user()->username,
                    'avatar_path' => Auth::user()->profile->avatar_path
                ], 'content' => [
                    'item_id' => $this->id,
                    'title' => $this->title,
                    'rating_id' => $this->rating_id,
                ],
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $full_name = Auth::user()->profile->full_name;
        return [
            'type' => "order",
            'to' => "buyer",
            'title' =>  " قام " . $full_name . " بالرد على تعليقك ",
            'title_ar' =>  " قام " . $full_name . " بالرد على تعليقك ",
            'title_en' =>  $full_name . " replied to your comment",
            'title_fr' => $full_name . "a répondu à votre commentaire",

            'user_sender' =>  [
                'full_name' => Auth::user()->profile->full_name,
                'username' => Auth::user()->username,
                'avatar_path' => Auth::user()->profile->avatar_path
            ],
            'content' => [
                'item_id' => $this->id,
                'title' => $this->title,
                'title_ar' => $this->title_ar,
                'title_en' => $this->title_en,
                'title_Fr' => $this->title_Fr,

            ],
        ];
    }
}
