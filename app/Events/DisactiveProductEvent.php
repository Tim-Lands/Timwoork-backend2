<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisactiveProductEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $product;
    public $cause;
    public $cause_ar;
    public $cause_en;
    public $cause_fr;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $product, $cause, $cause_ar, $cause_en, $cause_fr)
    {
        $this->user = $user;
        $this->product = $product;
        $this->cause = $cause;
        $this->cause_ar = $cause_ar;
        $this->cause_fr = $cause_en;
        $this->cause_fr = $cause_en;

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
            'title' =>  " لقد تم تعطيل خدمتك : " . $this->product->title ." و السبب هو : ".$this->cause,
            'content' => [
                'product_id' => $this->product->id,
                'title' => $this->product->title,
                'slug' => $this->product->slug,
                'cause' => $this->cause,
            ],
        ];
    }
}
