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
    public $product;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $product)
    {
        $this->user = $user;
        $this->product = $product;
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
            ->from('support@timlands.com')
            ->subject('قبول استلام العمل')
            ->view('emails.orders.dilevered_by_seller', [
                'type' => "product",
                'to' => "seller",
                'title' =>  " قام " . Auth::user()->profile->full_name . " بتقييم خدمتك ",
                'user_sender' => [
                    'full_name' => Auth::user()->profile->full_name,
                    'username' => Auth::user()->username,
                    'avatar_url' => Auth::user()->profile->avatar_url
                ],                'content' => [
                    'item_id' => $this->item->id,
                    'title' => $this->item->title,
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
        $seller = User::find($this->rate->user_id);
        return [
            'type' => "order",
            'to' => "seller",
            'title' =>  " قام " . Auth::user()->profile->full_name . " بشراء خدمة ",
            'user_sender' =>  [
                'full_name' => Auth::user()->profile->full_name,
                'username' => Auth::user()->username,
                'avatar_url' => Auth::user()->profile->avatar_url
            ],
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
            ],
        ];
    }
}
