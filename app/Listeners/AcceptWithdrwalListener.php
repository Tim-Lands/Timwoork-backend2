<?php

namespace App\Listeners;

use App\Events\AcceptWithdrwal;
use App\Notifications\AcceptWithdrawal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptWithdrwalListener
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
    public function handle(AcceptWithdrwal $event)
    {
        $event->user->notify(new AcceptWithdrawal($event->user, $event->withdrawal));
    }
}
