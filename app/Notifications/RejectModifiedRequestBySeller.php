<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RejectModifiedRequestBySeller extends Notification
{
    use Queueable;
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
            ->subject('رفض إلغاء الطلبية')
            ->view('emails.orders.reject_modified_request_by_seller', [
                'type' => "order",
                'to' => "buyer",
                'title' =>  " لم يقبل " . Auth::user()->profile->full_name . " برفض طلب التعديلات ",
                'user_sender' => Auth::user()->profile,
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
            'to' => "buyer",
            'title' =>  " لم يقبل " . Auth::user()->profile->full_name . " برفض طلب التعديلات ",
            'user_sender' => Auth::user()->profile,
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
            ],
        ];
    }
}
