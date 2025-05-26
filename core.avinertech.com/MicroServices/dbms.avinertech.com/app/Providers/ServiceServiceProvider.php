<?php

namespace App\Providers;

use App\Services\Contracts\ServiceInterface;
use App\Services\QueryCreatorService;
use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(ServiceInterface::class, function ($app) {
            return new QueryCreatorService();
        });

        $this->app->singleton(QueryCreatorService::class, function ($app) {
            return new QueryCreatorService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
} 