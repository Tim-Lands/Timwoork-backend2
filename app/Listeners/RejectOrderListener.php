<?php

namespace App\Listeners;

use App\Events\RejectOrder;
use App\Notifications\RejectOrder as NotificationsRejectOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RejectOrderListener
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
    public function handle(RejectOrder $event)
    {
        $event->user->notify(new NotificationsRejectOrder($event->user, $event->item));
    }
}
