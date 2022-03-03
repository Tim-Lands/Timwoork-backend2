<?php

namespace App\Listeners;

use App\Events\ResolveConflictBySeller;
use App\Notifications\ResolveConflictBySeller as NotificationsResolveConflictBySeller;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ResolveConflictListener
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
    public function handle(ResolveConflictBySeller $event)
    {
        $event->user->notify(new NotificationsResolveConflictBySeller($event->user, $event->item));
    }
}
