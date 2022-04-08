<?php

namespace App\Listeners;

use App\Events\RequestRejectOrder;
use App\Notifications\RequestRejectOrder as NotificationsRequestRejectOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestRejectOrderListener
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
    public function handle(RequestRejectOrder $event)
    {
        $event->user->notify(new NotificationsRequestRejectOrder($event->user, $event->item));
    }
}
