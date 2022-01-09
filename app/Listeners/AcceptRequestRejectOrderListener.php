<?php

namespace App\Listeners;

use App\Events\AcceptRequestRejectOrder;
use App\Notifications\AcceptRequestRejectOrder as NotificationsAcceptRequestRejectOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptRequestRejectOrderListener
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
    public function handle(AcceptRequestRejectOrder $event)
    {
        $event->user->notify(new NotificationsAcceptRequestRejectOrder($event->user, $event->item));
    }
}
