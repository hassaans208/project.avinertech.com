<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseConfiguration extends Model
{
    protected $fillable = [
        'name',
        'value',
        'type',
        'host'
    ];

    /**
     * Get all configurations for a host as an array
     *
     * @param string $host
     * @return array
     */
    public static function getConfigForHost(string $host): array
    {
        $configs = self::where('host', $host)->get();
        
        return $configs->mapWithKeys(function ($config) {
            $value = $config->value;
            
            // Cast value based on type
            switch ($config->type) {
                case 'integer':
                    $value = (int) $value;
                    break;
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'json':
                    $value = json_decode($value, true);
                    break;
            }
            
            return [$config->name => $value];
        })->toArray();
    }

    /**
     * Store or update configurations for a host
     *
     * @param string $host
     * @param array $configs
     * @return void
     */
    public static function storeConfigForHost(string $host, array $configs): void
    {
        $type = 'DATABASE';
        foreach ($configs as $name => $value) {

            
            self::updateOrCreate(
                [
                    'name' => $name,
                    'host' => $host
                ],
                [
                    'value' => $value,
                    'type' => $type
                ]
            );
        }
    }
} 