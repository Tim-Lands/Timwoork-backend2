<?php

namespace App\Listeners;

use App\Events\NewOrder;
use App\Notifications\NewOrder as NotificationsNewOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NewOrderListener
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
    public function handle(NewOrder $event)
    {
        $event->user->notify(new NotificationsNewOrder($event->user));
    }
}
