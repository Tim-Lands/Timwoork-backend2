<?php

namespace App\Listeners;

use App\Events\VerifyEmail;
use App\Notifications\EmailVerify;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class VerifyEmailListener
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
    public function handle(VerifyEmail $event)
    {
        $event->user->notify(new EmailVerify($event->user));
    }
}
