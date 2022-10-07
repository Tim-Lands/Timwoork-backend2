<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcceptProductNotification extends Notification
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
        return ['database'];
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
            ->subject('قبول الخدمة')
            ->view('emails.products.accept_product', [
                'user_sender' => [
                    'full_name' => 'اﻹدارة',
                    'username' => null,
                    'avatar_url' => null
                ],
                'title' =>  "لقد تم قبول خدمتك : " . $this->product->title,
                'content' => [
                    'product_id' => $this->product->id,
                    'slug' => $this->product->slug,
                    'title' => $this->product->title,
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
                'full_name_ar' => 'الادارة',
                'full_name_en' => 'the administration',
                'full_name_fr' => "L'administration",
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  " لقد تم قبول خدمتك بنجاح : " . $this->product->title,
            'title_ar' =>  " لقد تم قبول خدمتك بنجاح : " . $this->product->title_ar,
            'title_en' =>  " Your service has been successfully accepted: " . $this->product->title_en,
            'title_fr' =>  " Votre service a été accepté avec succès: " . $this->product->title_fr,

            'content' => [
                'product_id' => $this->product->id,
                'slug' => $this->product->slug,
                'title' => $this->product->title,
                'title_ar' => $this->product->title_ar,
                'title_en' => $this->product->title_en,
                'title_fr' => $this->product->title_fr,
            ],
        ];
    }
}
