<?php

namespace App\Providers;

use App\Events\PaymentAttemptFailed;
use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Events\PaymentInitiated;
use App\Listeners\PaymentAttemptFailedListener;
use App\Listeners\PaymentCompletedListener;
use App\Listeners\PaymentFailedListener;
use App\Listeners\PaymentInitiatedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaymentInitiated::class => [
            PaymentInitiatedListener::class,
        ],
        PaymentCompleted::class => [
            PaymentCompletedListener::class,
        ],
        PaymentFailed::class => [
            PaymentFailedListener::class,
        ],
        PaymentAttemptFailed::class => [
            PaymentAttemptFailedListener::class,
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