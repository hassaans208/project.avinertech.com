<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConfigurationService
{
    /**
     * Get configurations by type
     *
     * @param string $type
     * @return array
     */
    public function getConfigurations(string $type): array
    {
        // Try to get from cache first
        $cacheKey = "config_{$type}";
        if (Cache::has($cacheKey) && !empty(Cache::get($cacheKey))) {
            return Cache::get($cacheKey);
        }
        // Get from database
        $configurations = Configuration::where('type', strtoupper($type))->get();


        $configurations = $configurations->toArray();

        // Cache the results
        if(!empty($configurations)) {
            Cache::put($cacheKey, $configurations, now()->addHours(24));
        }

        return $configurations;
    }

    /**
     * Store configuration
     *
     * @param string $type
     * @param array $configurations
     * @return array
     * @throws \Exception
     */
    public function storeConfiguration(string $type, array $configurations): array
    {
        DB::beginTransaction();
        try {
            $storedConfigs = [];
            
            foreach ($configurations as $config) {

                $configuration = Configuration::updateOrCreate(
                    [
                        'type' => $type,
                        'name' => strtoupper($config['name'])
                    ],
                    [
                        'value' => $config['value'],
                        'host' => $config['host'] ?? null,
                        'group' => $config['group'] ?? null,
                        'type' => strtoupper($config['type']) ?? null,
                        // 'description' => $config['description'] ?? null
                    ]
                );

                $storedConfigs[] = [
                    'name' => $configuration->name,
                    'value' => $configuration->value,
                    'host' => $configuration->host,
                    'group' => $configuration->group,
                    'type' => $configuration->type,
                    // 'description' => $configuration->description
                ];
            }

            // Clear cache for this type
            Cache::forget("config_{$type}");

            DB::commit();
            return $storedConfigs;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
} 