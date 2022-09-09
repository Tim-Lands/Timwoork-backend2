<?php

namespace App\Listeners;

use App\Events\DeleteMessageEvent;
use App\Notifications\DeleteMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DeleteMessageListener
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
    public function handle(DeleteMessageEvent $event)
    {
        $event->user->notify(new DeleteMessageNotification(
            $event->user,
            $event->cause,
            $event->cause_ar,
            $event->cause_en,
            $event->cause_fr,
        ));
    }
}
