<?php

namespace App\Listeners;

use App\Events\CancelWithdrwal;
use App\Notifications\CancelWithdrwal as NotificationsCancelWithdrwal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CancelWithdrwalListener
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
    public function handle(CancelWithdrwal $event)
    {
        $event->user->notify(new NotificationsCancelWithdrwal($event->user, $event->withdrawal, $event->cause));
    }
}
