<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CancelWithdrwal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $withdrawal;
    public $cause;
    public $type;
    /**
     * Create a new event instance.
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
                $this->type = ' حسابك في بايبال';
                break;
            case 1:
                $this->type = 'حسابك في وايز';
                break;
            case 2:
                $this->type = 'حسابك البنكي';
                break;
            case 3:
                $this->type = 'الحوالة البنكية';
                break;
        }
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
            'user_sender' => [
                'full_name' => 'اﻹدارة',
                'username' => null,
                'avatar_url' => null
            ],
            'title' =>  " لقد تم رفض طلب السحب الخاص بك في  " . $this->type,
            'content' => [
                'type' => $this->type,
                'withdrawal' => $this->withdrawal,
                'cause' => $this->cause,
            ],
        ];
    }
}
