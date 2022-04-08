<?php

namespace App\Listeners;

use App\Events\CanceledOrderBySeller;
use App\Notifications\CanceledOrderBySeller as NotificationsCanceledOrderBySeller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CanceledOrderBySellerListener
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
    public function handle(CanceledOrderBySeller $event)
    {
        $event->user->notify(new NotificationsCanceledOrderBySeller($event->user, $event->item));
    }
}
