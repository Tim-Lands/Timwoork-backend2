<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BanAccountEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $comment;
    public $comment_ar;
    public $comment_en;
    public $comment_fr;
    public $expired_at;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $comment, $comment_ar, $comment_en, $comment_fr ,$expired_at)
    {
        $this->user = $user;
        $this->comment = $comment;
        $this->comment_ar = $comment_ar;
        $this->comment_en = $comment_en;
        $this->comment_fr = $comment_fr;
        $this->expired_at = $expired_at;
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
            'title' =>  "تم الحظر عن حسابك بسبب : " . $this->comment . " وتاريخ الحظر : " . $this->expired_at ? $this->expired_at : "لا يوجد تاريخ",
            'content' => [
                'user_name' => $this->user->username,
                'user_id' =>$this->user->id,
                'comment' => $this->comment,
                'expired_at' => $this->expired_at,
            ],
        ];
    }
}
