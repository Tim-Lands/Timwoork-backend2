<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BanAccountNotification extends Notification
{
    use Queueable;
    public $user;
    public $comment;
    public $expired_at;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $comment, $expired_at)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->expired_at = $expired_at;
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
        ->subject('رفع الحظر')
        ->view('emails.system.ban_user', [
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  "تم الحظر عن حسابك بسبب : " . $this->comment . " وتاريخ الحظر : " . $this->expired_at ? $this->expired_at : "لا يوجد تاريخ",
            'content' => [
                'user' => $this->user,
                'comment' => $this->comment,
                'expired_at' => $this->expired_at,
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
            'title' => "تم الحظر عن حسابك بسبب : " . $this->comment . " وتاريخ الحظر : " . $this->expired_at ? $this->expired_at : "لا يوجد تاريخ",
            'content' => [
                'user' => $this->user,
            ],
        ];
    }
}
