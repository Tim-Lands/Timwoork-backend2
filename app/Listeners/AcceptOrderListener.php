<?php

namespace App\Listeners;

use App\Events\AcceptOrder;
use App\Notifications\AcceptOrder as NotificationsAcceptOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptOrderListener
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
    public function handle(AcceptOrder $event)
    {
        $event->user->notify(new NotificationsAcceptOrder($event->user, $event->item));
    }
}
