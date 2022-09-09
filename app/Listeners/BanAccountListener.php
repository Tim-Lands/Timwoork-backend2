<?php

namespace App\Listeners;

use App\Events\BanAccountEvent;
use App\Notifications\BanAccountNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BanAccountListener
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
    public function handle(BanAccountEvent $event)
    {
        $event->user->notify(new BanAccountNotification(
            $event->user,
            $event->comment,
            $event->comment_ar,
            $event->comment_en,
            $event->comment_fr,
            $event->expired_at));
    }
}
