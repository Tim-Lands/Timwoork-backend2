<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class RejectOrder extends Notification
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
        return [   'database'];
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
            ->subject('رفض الطلبية')
            ->view('emails.orders.reject_order', [
                'type' => "order",
                'to' => "buyer",
                'title' =>  " قام " . Auth::user()->profile->full_name . " بإلغاء الطلبية   ",
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
        $full_name = Auth::user()->profile->full_name;
        return [
            'type' => "order",
            'to' => "buyer",
            'title' =>  " قام " . $full_name . " بإلغاء عملية الشراء التي قام بها",
            'title_ar' =>  " قام " . $full_name . " بإلغاء عملية الشراء التي قام بها",
            'title_en' => $full_name . " has canceled his purchase",
            'title_fr' =>   $full_name . " has canceled his purchase",

            'user_sender' => [
                'full_name' => $full_name,
                'username' => Auth::user()->username,
                'avatar_path' => Auth::user()->profile->avatar_path
            ],
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
                'title_ar' => $this->item->title_ar,
                'title_en' => $this->item->title_en,
                'title_fr' => $this->item->title_fr,

            ],
        ];
    }
}
