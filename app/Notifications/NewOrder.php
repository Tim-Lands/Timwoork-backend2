<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class NewOrder extends Notification implements ShouldQueue
{
    use Queueable;
    public $user;
    public $item;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $item)
    {
        $this->user = $user;
        $this->item = $item;
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
        ->subject('طلبية جديدة')
            ->view('emails.orders.new_order', [
                'type' => "order",
                'to' => "seller",
                'title' =>  " قام " . Auth::user()->profile->full_name . " بشراء خدمة من خدماتك ",
                'user_sender' =>  [
                    'full_name' => Auth::user()->profile->full_name,
                    'username' => Auth::user()->username,
                    'avatar_url' => Auth::user()->profile->avatar_path
                ],
                'content' => [
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
        return [
            'type' => "order",
            'to' => "seller",
            'title' =>  " قام " . Auth::user()->profile->full_name . " بشراء خدمة ",
            'user_sender' =>  [
                'full_name' => Auth::user()->profile->full_name,
                'username' => Auth::user()->username,
                'avatar_url' => Auth::user()->profile->avatar_path
            ],
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
            ],
        ];
    }
}
