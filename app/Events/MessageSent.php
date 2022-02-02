<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $receiver = $this->message->conversation->members()
            ->where('user_id', '<>', Auth::id())
            ->first();
        return new PresenceChannel('receiver.' . $receiver->id);
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }


    public function broadcastWith()
    {
        return [
            'message' =>  $this->message->load('user.profile', 'conversation'),
            'unreaded_messages_count' =>  $this->message->conversation->messages()
                ->whereNull('read_at')->where('user_id', '<>', Auth::id())->count(),
        ];
    }
}
