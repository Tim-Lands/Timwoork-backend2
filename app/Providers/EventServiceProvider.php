<?php

namespace App\Providers;

use App\Events\ForgetPassword;
use App\Events\NewOrder;
use App\Events\VerifyEmail;
use App\Listeners\ForgetPasswordListener;
use App\Listeners\NewOrderListener;
use App\Listeners\VerifyEmailListener;
use App\Models\Item;
use App\Observers\ItemObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        VerifyEmail::class => [
            VerifyEmailListener::class,
        ],
        ForgetPassword::class => [
            ForgetPasswordListener::class,
        ],
        NewOrder::class => [
            NewOrderListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Item::observe(ItemObserver::class);
    }
}
