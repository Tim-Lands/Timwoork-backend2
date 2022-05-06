<?php

namespace App\Listeners;

use App\Events\RejectProductEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RejectProductListener
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
    public function handle(RejectProductEvent $event)
    {
        $event->user->notify(new RejectProductEvent($event->user, $event->product, $this->cause));
    }
}
