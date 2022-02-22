<?php

namespace App\Providers;

use App\Events\AcceptedDileveredByBuyer;
use App\Events\AcceptOrder;
use App\Events\AcceptRequestRejectOrder;
use App\Events\CanceledOrderByBuyer;
use App\Events\DileveredBySeller;
use App\Events\ForgetPassword;
use App\Events\NewOrder;
use App\Events\RejectOrder;
use App\Events\RejectRequestRejectOrder;
use App\Events\RequestRejectOrder;
use App\Events\VerifyEmail;
use App\Listeners\AcceptedDileveredByBuyerListener;
use App\Listeners\AcceptOrderListener;
use App\Listeners\AcceptRequestRejectOrderListener;
use App\Listeners\CanceledOrderByBuyerListener;
use App\Listeners\CanceledOrderListener;
use App\Listeners\DileveredBySellerListener;
use App\Listeners\ForgetPasswordListener;
use App\Listeners\NewOrderListener;
use App\Listeners\RejectOrderListener;
use App\Listeners\RejectRequestRejectOrderListener;
use App\Listeners\RequestRejectOrderListener;
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
        AcceptOrder::class => [
            AcceptOrderListener::class,
        ],
        CanceledOrderByBuyer::class => [
            CanceledOrderByBuyerListener::class,
        ],
        RejectOrder::class => [
            RejectOrderListener::class,
        ],
        DileveredBySeller::class => [
            DileveredBySellerListener::class,
        ],
        AcceptedDileveredByBuyer::class => [
            AcceptedDileveredByBuyerListener::class
        ],
        RequestRejectOrder::class => [
            RequestRejectOrderListener::class,
        ],
        AcceptRequestRejectOrder::class => [
            AcceptRequestRejectOrderListener::class,
        ],
        RejectRequestRejectOrder::class => [
            RejectRequestRejectOrderListener::class,
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
