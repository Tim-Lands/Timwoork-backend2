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
    public $cause_ar;
    public $cause_en;
    public $cause_fr;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $product, $cause, $cause_ar, $cause_en, $cause_fr)
    {
        $this->user = $user;
        $this->product = $product;
        $this->cause = $cause;
        $this->cause_ar = $cause_ar;
        $this->cause_en = $cause_en;
        $this->cause_fr = $cause_fr;

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
            'title_ar' =>  "لقد تم تعطيل خدمتك : " . $this->product->title . " و السبب هو :".$this->cause,
            'title_en' =>  "Your service has been disabled: " . $this->product->title . " The reason is:".$this->cause,
            'title_fr' =>  "Votre service a été désactivé: " . $this->product->title . " La raison est:".$this->cause,

            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
                'title_ar' => $this->product->title_ar,
                'title_en' => $this->product->title_en,
                'title_fr' => $this->product->title_fr,
                'slug' => $this->product->slug,
                "cause" => $this->cause,
                "cause_ar" => $this->cause_ar,
                "cause_en" => $this->cause_en,
                "cause_fr" => $this->cause_fr,

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
                'slug' => $this->product->slug,
                "cause" => $this->cause
            ],
        ];
    }
}
