<?php

namespace App\Listeners;

use App\Events\AcceptModifiedBySeller;
use App\Notifications\AcceptModifiedBySeller as NotificationsAcceptModifiedBySeller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptModifiedBySellerListener
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
    public function handle(AcceptModifiedBySeller $event)
    {
        $event->user->notify(new NotificationsAcceptModifiedBySeller($event->user, $event->item));
    }
}
