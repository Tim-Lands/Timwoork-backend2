<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CancelWithdrwal extends Notification
{
    use Queueable;
    public $user;
    public $withdrawal;
    public $cause;
    public $type;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $withdrawal, $cause)
    {
        $this->user = $user;
        $this->withdrawal = $withdrawal;
        $this->cause = $cause;
        switch ($this->withdrawal->type) {
            case 0:
                $this->type = ' حسابك في بايبال ';
                break;
            case 1:
                $this->type = 'حسابك في وايز ';
                break;
            case 2:
                $this->type = 'حسابك البنكي ';
                break;
            case 3:
                $this->type = ' الحوالة البنكية ';
                break;
        }
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
            ->subject('قبول طلب السحب')
            ->view('emails.system.cancel_withdrawal', [
                'user_sender' => [
                    'full_name' => 'اﻹدارة',
                    'username' => null,
                    'avatar_url' => null
                ],
                'title' =>  " لقد تم رفض طلب السحب الخاص بك في " . $this->type,
                'content' => [
                    'type' => $this->type,
                    'withdrawal' => $this->withdrawal,
                    'cause' => $this->cause,
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
            'title' =>  " لقد تم رفض طلب السحب الخاص بك في " . $this->type,
            'content' => [
                'type' => $this->type,
                'withdrawal' => $this->withdrawal,
                'cause' => $this->cause,
            ],
        ];
    }
}
