<?php

namespace App\Listeners;

use App\Events\DileveredBySeller;
use App\Notifications\DileveredBySeller as NotificationsDileveredBySeller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DileveredBySellerListener
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
    public function handle(DileveredBySeller $event)
    {
        $event->user->notify(new NotificationsDileveredBySeller($event->user, $event->item));
    }
}
