<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\TenantRepository;
use App\Repositories\PackageRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their concrete implementations
        $this->app->bind(TenantRepositoryInterface::class, TenantRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 