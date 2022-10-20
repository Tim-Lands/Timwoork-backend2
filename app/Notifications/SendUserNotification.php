<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendUserNotification extends Notification
{
    use Queueable;
    public $user;
    public $cause;
    public $cause_ar;
    public $cause_en;
    public $cause_fr;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $cause, $cause_ar, $cause_en, $cause_fr)
    {
        $this->user = $user;
        $this->cause = $cause;
        $this->cause_ar = $cause_ar;
        $this->cause_fr = $cause_fr;
        $this->cause_en = $cause_en;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [/*  */ 'database'];
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
            ->subject('إشعار')
            ->view('emails.products.reject_product', [
                'user_sender' => [
                    'full_name' => 'اﻹدارة',
                    'username' => null,
                    'avatar_url' => null
                ],
                'title' => "لقد تم إرسال إشعار إليك من قبل الإدارة",
                'content' => [
                    'title' => $this->cause,
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
                'full_name_ar' => 'اﻹدارة',
                'full_name_en' => 'Administration',
                'full_name_fr' => 'Administration',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "لقد تم إرسال إشعار إليك من قِبل الإدارة",
            'content' => [
                'user' => $this->user,
                'cause' => $this->cause,
                'cause_ar' => $this->cause_ar,
                'cause_en' => $this->cause_en,
                'cause_fr' => $this->cause_fr,

            ],
        ];
    }
}
