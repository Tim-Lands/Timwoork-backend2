<?php

namespace App\Listeners;

use App\Events\ForgetPassword;
use App\Notifications\ForgetPassword as NotificationsForgetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ForgetPasswordListener
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
    public function handle(ForgetPassword $event)
    {
        $event->user->notify(new NotificationsForgetPassword($event->user));
    }
}
