<?php

namespace App\Listeners;

use App\Events\AcceptedDileveredByBuyer;
use App\Notifications\AcceptedDileveredByBuyer as NotificationsAcceptedDileveredByBuyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptedDileveredByBuyerListener
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
    public function handle(AcceptedDileveredByBuyer $event)
    {
        $event->user->notify(new NotificationsAcceptedDileveredByBuyer($event->user, $event->item));
    }
}
