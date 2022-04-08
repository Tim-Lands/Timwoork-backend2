<?php

namespace App\Listeners;

use App\Events\CanceledOrderByBuyer;
use App\Notifications\CanceledOrderByBuyer as NotificationsCanceledOrderByBuyer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CanceledOrderByBuyerListener
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
    public function handle(CanceledOrderByBuyer $event)
    {
        $event->user->notify(new NotificationsCanceledOrderByBuyer($event->user, $event->item));
    }
}
