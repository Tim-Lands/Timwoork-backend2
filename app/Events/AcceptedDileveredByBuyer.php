<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptedDileveredByBuyer implements ShouldBroadcast
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
        $buyer = $this->item->order->cart->user;
        return [
            'type' => "order",
            'to' => 'seller',
            'notifications_count' => $this->user->unreadNotifications->count(),
            'title' =>  " قام " . $buyer->profile->full_name . " باستلام العمل ",
            'user_sender' => [
                'full_name' => $buyer->profile->full_name,
                'username' => $buyer->username,
                'avatar_url' => $buyer->profile->avatar_path
            ],
            'content' => [
                'item_id' => $this->item->id,
                'title' => $this->item->title,
            ],
        ];
    }
}
