<?php

namespace App\Listeners;

use App\Events\RequestModifiedBuBuyer;
use App\Notifications\RequestModifiedByBuyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestModifiedByBuyerListener
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
    public function handle(RequestModifiedBuBuyer $event)
    {
        $event->user->notify(new RequestModifiedByBuyer($event->user, $event->item));
    }
}
