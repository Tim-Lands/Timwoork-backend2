<?php

namespace App\Listeners;

use App\Events\Rating as EventsRating;
use App\Notifications\Rating as NotificationsRating;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class Rating
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(EventsRating $event)
    {
        $event->user->notify(new NotificationsRating($event->user, $event->product));
    }
}
