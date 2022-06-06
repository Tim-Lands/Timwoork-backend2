<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendCurrency implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $data_currency;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data_currency)
    {
        $this->data_currency = $data_currency;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // public channel
        return new Channel('currency');
    }

    public function broadcastAs()
    {
        return 'currency';
    }

    public function broadcastWith()
    {
        return [
            'data_currency' => $this->data_currency,
        ];
    }
}
