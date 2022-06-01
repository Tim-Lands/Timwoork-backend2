<?php

namespace App\Listeners;

use App\Events\UnbanAccountEvent;
use App\Notifications\UnbanAccountNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UnbanAccountListener
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
    public function handle(UnbanAccountEvent $event)
    {
        $event->user->notify(new UnbanAccountNotification($event->user));
    }
}
