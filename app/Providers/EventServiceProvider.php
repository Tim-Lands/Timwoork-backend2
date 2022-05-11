<?php

namespace App\Providers;

use App\Events\AcceptedDileveredByBuyer;
use App\Events\AcceptModifiedBySeller;
use App\Events\AcceptOrder;
use App\Events\AcceptProductEvent;
use App\Events\AcceptRequestRejectOrder;
use App\Events\AcceptWithdrwal;
use App\Events\CanceledOrderByBuyer;
use App\Events\CancelWithdrwal;
use App\Events\DileveredBySeller;
use App\Events\ForgetPassword;
use App\Events\NewOrder;
use App\Events\Rating;
use App\Events\RejectModifiedRequestBySeller;
use App\Events\RejectOrder;
use App\Events\RejectProductEvent;
use App\Events\RejectRequestRejectOrder;
use App\Events\Reply;
use App\Events\RequestModifiedBuBuyer;
use App\Events\RequestRejectOrder;
use App\Events\ResolveConflictBySeller;
use App\Events\VerifyEmail;
use App\Listeners\AcceptedDileveredByBuyerListener;
use App\Listeners\AcceptModifiedBySellerListener;
use App\Listeners\AcceptOrderListener;
use App\Listeners\AcceptProductListener;
use App\Listeners\AcceptRequestRejectOrderListener;
use App\Listeners\AcceptWithdrwalListener;
use App\Listeners\CanceledOrderByBuyerListener;
use App\Listeners\CanceledOrderListener;
use App\Listeners\CancelWithdrwalListener;
use App\Listeners\DileveredBySellerListener;
use App\Listeners\ForgetPasswordListener;
use App\Listeners\NewOrderListener;
use App\Listeners\Rating as ListenersRating;
use App\Listeners\RejectModifiedRequestBySellerListener;
use App\Listeners\RejectOrderListener;
use App\Listeners\RejectProductListener;
use App\Listeners\RejectRequestRejectOrderListener;
use App\Listeners\Reply as ListenersReply;
use App\Listeners\RequestModifiedByBuyerListener;
use App\Listeners\RequestRejectOrderListener;
use App\Listeners\ResolveConflictListener;
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
        ResolveConflictBySeller::class => [
            ResolveConflictListener::class,
        ],
        RequestModifiedBuBuyer::class => [
            RequestModifiedByBuyerListener::class,
        ],
        AcceptModifiedBySeller::class => [
            AcceptModifiedBySellerListener::class,
        ],
        RejectModifiedRequestBySeller::class => [
            RejectModifiedRequestBySellerListener::class,
        ],

        Rating::class => [
            ListenersRating::class,
        ],
        Reply::class => [
            ListenersReply::class,
        ],
        AcceptWithdrwal::class => [
            AcceptWithdrwalListener::class,
        ],
        CancelWithdrwal::class => [
            CancelWithdrwalListener::class,
        ],
        AcceptProductEvent::class => [
            AcceptProductListener::class,
        ],
        RejectProductEvent::class => [
            RejectProductListener::class,
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
