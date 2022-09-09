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
    public $comment_ar;
    public $comment_en;
    public $comment_fr;
    public $expired_at;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $comment, $comment_ar, $comment_en, $comment_fr , $expired_at)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->comment_ar = $comment_ar;
        $this->comment_en = $comment_en;
        $this->comment_fr = $comment_fr;
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
                'full_name_ar'=>'الإدارة',
                'full_name_en'=>'Administration',
                'full_name_fr'=>'Administration',
                'username' => null,
                'avatar_url' => null
            ],
            'title' => "تم الحظر عن حسابك بسبب : " . $this->comment . " وتاريخ الحظر : " . $this->expired_at ? $this->expired_at : "لا يوجد تاريخ",
            'title_ar' => "تم الحظر عن حسابك بسبب : " . $this->comment->ar . " وتاريخ الحظر : " . $this->expired_at ? $this->expired_at : "لا يوجد تاريخ",
            'title_en' => "Your account has been banned because: " . $this->comment->en . " ban date : " . $this->expired_at ? $this->expired_at : "No date",
            'title_fr' => "Votre compte a été banni car: " . $this->comment->fr . " date d'interdiction: " . $this->expired_at ? $this->expired_at : "Pas de date",
            'content' => [
                'user' => $this->user,
            ],
        ];
    }
}
