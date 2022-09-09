<?php

namespace App\Listeners;

use App\Events\DisactiveProductEvent;
use App\Events\RejectProductEvent;
use App\Notifications\DisactiveProductNotification;
use App\Notifications\RejectProductNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DisactiveProductListener
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
    public function handle(DisactiveProductEvent $event)
    {
        $event->user->notify(new DisactiveProductNotification(
            $event->user,
            $event->product,
            $event->cause,
            $event->cause_ar,
            $event->cause_en,
            $event->cause_fr,


        ));
    }
}
