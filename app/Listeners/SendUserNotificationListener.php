<?php

namespace App\Listeners;

use App\Events\DisactiveProductEvent;
use App\Events\RejectProductEvent;
use App\Notifications\DisactiveProductNotification;
use App\Notifications\RejectProductNotification;
use App\Events\SendUserNotificationEvent;
use App\Notifications\SendUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserNotificationListener
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
    public function handle(SendUserNotificationEvent $event)
    {
        $event->user->notify(new SendUserNotification($event->user, $event->cause));
    }
}
