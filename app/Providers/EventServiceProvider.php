<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderShipped;
use App\Events\ReturnStatusChanged;
use App\Listeners\RecordLoginAttempt;
use App\Listeners\SendOrderConfirmationEmail;
use App\Listeners\SendReturnStatusEmail;
use App\Listeners\SendShippingUpdateEmail;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Login::class => [
            RecordLoginAttempt::class,
        ],
        OrderCreated::class => [
            SendOrderConfirmationEmail::class,
        ],
        OrderShipped::class => [
            SendShippingUpdateEmail::class,
        ],
        ReturnStatusChanged::class => [
            SendReturnStatusEmail::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
