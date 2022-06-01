<?php

namespace App\Listeners;

use App\Events\AcceptProductEvent;
use App\Notifications\AcceptProductNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptProductListener
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
    public function handle(AcceptProductEvent $event)
    {
        $event->user->notify(new AcceptProductNotification($event->user, $event->product));
    }
}
