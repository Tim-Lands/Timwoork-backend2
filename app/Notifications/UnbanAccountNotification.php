<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnbanAccountNotification extends Notification
{
    use Queueable;
    public $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [  'database'];
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
        ->subject('رفع الحظر')
        ->view('emails.system.unban_user', [
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' => "تم رفع الحظر عن حسابك",
            'content' => [
                'user' => $this->user,
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
            'title' => "تم رفع الحظر عن حسابك",
            'title_ar' => "تم رفع الحظر عن حسابك",
            'title_en' => "Your account has been unbanned",
            'title_fr' => "Votre compte a été débloqué",

            'content' => [
                'user' => $this->user,
            ],
        ];
    }
}
