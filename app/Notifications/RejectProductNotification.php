<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RejectProductNotification extends Notification
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
        return [ 'database'];
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
        ->subject('رفض الخدمة')
        ->view('emails.products.reject_product', [
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "لقد تم رفض خدمتك : " . $this->product->title . " و السبب هو :".$this->cause,
            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
                'slug' => $this->product->slug,
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
        $title= $this->product->title;
        $title_ar= $this->product->title_ar;
        $title_en= $this->product->title_en;
        $title_fr= $this->product->title_fr;

        return [
            'type' => "system",
            'to' => "user",
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'full_name_ar'=>'الإدارة',
                'full_name_en'=>'Administration',
                'full_name_fr'=>'Administration',

                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "لقد تم رفض خدمتك : " . $title . " و السبب هو :".$this->cause,
            'title' =>  "لقد تم رفض خدمتك : " . $title_ar . " و السبب هو :".$this->cause_ar,
            'title_en' =>  "Your service has been denied: " . $title_en . " The reason is:".$this->cause_en,
            'title_fr' =>  "Votre service a été refusé: " . $title_fr . " La raison est:".$this->cause_fr,

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
        ];
    }
}
