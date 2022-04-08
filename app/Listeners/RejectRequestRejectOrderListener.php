<?php

namespace App\Listeners;

use App\Events\RejectRequestRejectOrder;
use App\Notifications\RejectRequestRejectOrder as NotificationsRejectRequestRejectOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RejectRequestRejectOrderListener
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
    public function handle(RejectRequestRejectOrder $event)
    {
        $event->user->notify(new NotificationsRejectRequestRejectOrder($event->user, $event->item));
    }
}
