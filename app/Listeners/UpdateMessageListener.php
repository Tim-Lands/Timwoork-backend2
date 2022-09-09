<?php

namespace App\Listeners;

use App\Events\UpdateMessageEvent;
use App\Notifications\UpdateMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateMessageListener
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
    public function handle(UpdateMessageEvent $event)
    {
        $event->user->notify(new UpdateMessageNotification(
            $event->user,
             $event->cause,
             $event->cause_ar,
             $event->cause_en,
             $event->cause_fr,

        ));
    }
}
