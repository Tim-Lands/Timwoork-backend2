<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DisactiveProductNotification extends Notification
{
    use Queueable;
    public $user;
    public $product;
    public $cause;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $product, $cause)
    {
        $this->user = $user;
        $this->product = $product;
        $this->cause = $cause;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
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
        ->subject('تعطيل الخدمة')
        ->view('emails.products.reject_product', [
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "لقد تم تعطيل خدمتك : " . $this->product->title . " و السبب هو :".$this->cause,
            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
                "cause" => $this->cause
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
            'type' => "system",
            'to' => "user",
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "لقد تم تعطيل خدمتك : " . $this->product->title . " و السبب هو :".$this->cause,
            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
                "cause" => $this->cause
            ],
        ];
    }
}
