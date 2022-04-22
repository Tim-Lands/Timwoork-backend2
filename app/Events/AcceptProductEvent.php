<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AcceptProductEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $product;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $product)
    {
        $this->user = $user;
        $this->product = $product;
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
            ],
            'title' =>  " لقد تم قبول خدمتك : " . $this->product->title,
            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
            ],
        ];
    }
}
