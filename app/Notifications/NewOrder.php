<?php

namespace App\Notifications;

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
            ->from('support@timlands.com')
            ->subject('طلبية جديدة')
            ->view('emails.orders.new_order', [
                'user' => Auth::user(),
                'item' => $this->item,
                'title' => ' قام ' . Auth::user()->profile->full_name . ' بطلب خدمة جديدة ',
                'type' => "new_order"
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
            'user' => Auth::user(),
            'item' => $this->item,
            'title' => 'قام ' . Auth::user()->profile->full_name . 'بطلب خدمة جديدة',
            'type' => "new_order"
        ];
    }
}
