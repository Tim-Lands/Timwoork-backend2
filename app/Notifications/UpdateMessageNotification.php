<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateMessageNotification extends Notification
{
    use Queueable;
    public $user;
    public $cause;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $cause)
    {
        $this->user = $user;
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
          ->subject('حذف الرسالة')
          ->view('emails.system.update_message', [
              'user_sender' => [
                  'full_name' => 'اﻹدارة',
              ],
              'title' => "تم التعديل على رسالتك من طرف الطرف الادارة و ذلك بسبب :". $this->cause,
              'content' => [
                  'user' => $this->user,
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
              ],
              'title' => "تم التعديل على رسالتك من طرف الطرف الادارة و ذلك بسبب :". $this->cause,
              'content' => [
                  'user' => $this->user,
                  'cause' => $this->cause,
              ],
          ];
    }
}
