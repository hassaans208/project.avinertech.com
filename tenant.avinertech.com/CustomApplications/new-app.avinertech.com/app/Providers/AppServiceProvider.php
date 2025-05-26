<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Get current host
        $host = request()->getHost();

        // Check if database config already exists
        // $existingConfig = Configuration::getDatabaseConfig($host);

        // if (!$existingConfig) {
        //     try {
        //         $response = Http::get("http://manager.avinertech.local/api/tenant/{$host}");
        //         $tenant = $response->json();
        //         $tenant = $tenant['data'];

        //         if ($tenant) {
        //             Configuration::storeConfigForHost($host, [
        //                 'database_host' => $tenant['database_host'],
        //                 'database_port' => $tenant['database_port'],
        //                 'database_name' => $tenant['database_name'],
        //                 'database_user' => $tenant['database_user'],
        //                 'database_password' => $tenant['database_password']
        //             ]);
        //         }
        //     } catch (\Exception $e) {
        //         \Log::error('Failed to fetch tenant configuration: ' . $e->getMessage());
        //     }
        // }
    }
}
