<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Rating extends Notification
{
    use Queueable;
    public $user;
    public $slug;
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
    public function __construct($user, $slug, $title, $title_ar, $title_en, $title_fr, $rating_id)
    {
        $this->user = $user;
        $this->slug = $slug;
        $this->title = $title;
        $this->title_ar = $title_ar;
        $this->title_en = $title;
        $this->title_fr = $title_fr;
        $this->$rating_id = $rating_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
            ->subject('تقييم الخدمة')
            ->view('emails.products.rating', [
                'type' => "rating",
                'to' => "seller",
                'title' =>  " قام " . Auth::user()->profile->full_name . " بتقييم خدمتك ",
                'user_sender' => [
                    'full_name' => Auth::user()->profile->full_name,
                    'username' => Auth::user()->username,
                    'avatar_path' => Auth::user()->profile->avatar_path
                ],                'content' => [
                    'slug' => $this->slug,
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
        $full_name = Auth::user()->full_name;
        return [
            'type' => "rating",
            'to' => "seller",
            'title' =>  " قام " . $full_name . "بتقييم خدمتك",
            'title_ar' =>  " قام " . $full_name . "بتقييم خدمتك",
            'title_en' =>  $full_name . "rated your service",
            'title_fr' => $full_name . " a évalué votre servicu",
            'user_sender' =>  [
                'full_name' => Auth::user()->profile->full_name,
                'username' => Auth::user()->username,
                'avatar_path' => Auth::user()->profile->avatar_path
            ],
            'content' => [
                'slug' => $this->slug,
                'title' => $this->title,
                'title_ar' => $this->title_ar,
                'title_en' => $this->title_en,
                'title_fr' => $this->title_fr,
                'rating_id' => $this->rating_id,
            ],
        ];
    }
}
