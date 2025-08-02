<?php

namespace App\Providers;

use App\Repositories\PaymentMethodRepository;
use App\Repositories\PaymentMethodRepositoryInterface;
use App\Repositories\PaymentTransactionRepository;
use App\Repositories\PaymentTransactionRepositoryInterface;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(PaymentTransactionRepositoryInterface::class, PaymentTransactionRepository::class);

        // Register PaymentService as singleton
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(PaymentMethodRepositoryInterface::class),
                $app->make(PaymentTransactionRepositoryInterface::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Tag payment controllers for discovery
        $this->app->tag([
            'App\Http\Controllers\Payment\StripePaymentController',
            'App\Http\Controllers\Payment\PaypalPaymentController', 
            'App\Http\Controllers\Payment\LocalBankPaymentController',
        ], 'payment.driver');
    }
} 