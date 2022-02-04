<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundAmount implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $item;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $item)
    {
        $this->user = $user;
        $this->item = $item;
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
        $seller = User::find($this->item->user_id);
        return [
            'type' => "order",
            'to' => 'seller',
            'title' =>  " قام " . $seller->profile->full_name . " بشراء خدمة ",
            'user_sender' => [
                'notifications_count' => $seller->unreadNotifications->count(),
                'full_name' => $seller->profile->full_name,
                'username' => $seller->username,
                'avatar_url' => $seller->profile->avatar_url
            ],
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
            ],
        ];
    }
}
