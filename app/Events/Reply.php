<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class Reply implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $id;
    public $title;
    public $rating_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $id, $title, $rating_id)
    {
        $this->user = $user;
        $this->id = $id;
        $this->title = $title;
        $this->rating_id = $rating_id;
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
        $buyer = Auth::user();
        return [
            'type' => "rating",
            'to' => 'seller',
            'notifications_count' => $this->user->unreadNotifications->count(),
            'title' =>  " قام " . $buyer->profile->full_name . " بالرد على تعليقك ",
            'user_sender' => [
                'full_name' => $buyer->profile->full_name,
                'username' => $buyer->username,
                'avatar_path' => $buyer->profile->avatar_path
            ],
            'content' => [
                'item_id'     => $this->id,
                'title'       => $this->title,
                'rating_id'   => $this->rating_id
            ],
        ];
    }
}
