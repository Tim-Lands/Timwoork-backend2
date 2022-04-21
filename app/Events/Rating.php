<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class Rating implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $slug;
    public $title;
    public $rating_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user, $slug, $title, $rating_id)
    {
        $this->user = $user;
        $this->slug = $slug;
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
            'title' =>  " قام " . $buyer->profile->full_name . " بتقييم خدمتك ",
            'user_sender' => [
                'full_name' => $buyer->profile->full_name,
                'username' => $buyer->username,
                'avatar_url' => $buyer->profile->avatar_path
            ],
            'content' => [
                'slug' => $this->slug,
                'title' => $this->title,
                'rating_id' => $this->rating_id,
            ],
        ];
    }
}
