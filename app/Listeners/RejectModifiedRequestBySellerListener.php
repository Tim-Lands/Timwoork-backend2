<?php

namespace App\Listeners;

use App\Events\RejectModifiedRequestBySeller;
use App\Notifications\RejectModifiedRequestBySeller as NotificationsRejectModifiedRequestBySeller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RejectModifiedRequestBySellerListener
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
    public function handle(RejectModifiedRequestBySeller $event)
    {
        $event->user->notify(new NotificationsRejectModifiedRequestBySeller($event->user, $event->item));
    }
}
