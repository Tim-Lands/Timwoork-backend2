<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $cause;
    public $cause_ar;
    public $cause_en;
    public $cause_fr;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $cause, $cause_ar, $cause_en, $cause_fr)
    {
        $this->user = $user;
        $this->cause = $cause;
        $this->cause_ar = $cause_ar;
        $this->cause_en = $cause_en;
        $this->cause_fr = $cause_fr;

    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('notify.' . $this->user->id);
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }

    public function broadcastWith()
    {
        return [
            'type' => "system",
            'to' => "user",
            'notifications_count' => $this->user->unreadNotifications->count(),
            'user_sender' => [
                'full_name' => 'اﻹدارة',
            ],
            'title' =>  "تم التعديل على رسالتك من طرف الطرف الادارة و ذلك بسبب :". $this->cause,
            'content' => [
                'user' => $this->user,
                'cause' => $this->cause
            ],
        ];
    }
}
