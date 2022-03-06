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
    public $rating_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $slug, $title, $rating_id)
    {
        $this->user = $user;
        $this->slug = $slug;
        $this->title = $title;
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
                    'avatar_url' => Auth::user()->profile->avatar_url
                ],                'content' => [
                    'item_id' => $this->slug,
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
        return [
            'type' => "order",
            'to' => "seller",
            'title' =>  " قام " . Auth::user()->profile->full_name . "بتقييم خدمتك",
            'user_sender' =>  [
                'full_name' => Auth::user()->profile->full_name,
                'username' => Auth::user()->username,
                'avatar_url' => Auth::user()->profile->avatar_url
            ],
            'content' => [
                'item_id' => $this->slug,
                'title' => $this->title,
                'rating_id' => $this->rating_id,
            ],
        ];
    }
}
