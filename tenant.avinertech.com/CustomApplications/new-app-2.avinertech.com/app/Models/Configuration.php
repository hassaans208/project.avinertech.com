<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Configuration extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'value',
        'host',
        'group',
        'type',
        'is_encrypted'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string',
        'description' => 'string'
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return 'configurations';
    }

    /**
     * Get all configurations for a host and type
     *
     * @param string $host
     * @param string $type
     * @return array
     */
    public static function getConfig(string $host, string $type = 'app'): array
    {
        $configs = self::where('host', $host)
            ->where('type', $type)
            ->get();
        
        return $configs->mapWithKeys(function ($config) {
            $value = $config->value;
            
            // Decrypt if needed
            if ($config->is_encrypted) {
                $value = Crypt::decryptString($value);
            }
            
            // Cast value based on type
            if ($config->type === 'database') {
                switch ($config->name) {
                    case 'db_port':
                        $value = (int) $value;
                        break;
                    case 'db_ssl':
                        $value = (bool) $value;
                        break;
                }
            }
            
            return [$config->name => $value];
        })->toArray();
    }

    /**
     * Get database configuration for a host
     *
     * @param string $host
     * @return array
     */
    public static function getDatabaseConfig(string $host): array
    {
        return self::getConfig($host, 'DATABASE');
    }

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

    /**
     * Store or update configurations
     *
     * @param string $host
     * @param array $configs
     * @param string $type
     * @param string|null $group
     * @param array $encryptedKeys
     * @return void
     */
    public static function storeConfig(
        string $host, 
        array $configs, 
        string $type = 'app', 
        ?string $group = null,
        array $encryptedKeys = []
    ): void {
        foreach ($configs as $name => $value) {
            $shouldEncrypt = in_array($name, $encryptedKeys);
            
            if ($shouldEncrypt && !empty($value)) {
                $value = Crypt::encryptString($value);
            }
            
            self::updateOrCreate(
                [
                    'name' => $name,
                    'type' => $type,
                    'host' => $host
                ],
                [
                    'value' => $value,
                    'group' => $group,
                    'is_encrypted' => $shouldEncrypt
                ]
            );
        }
    }

    /**
     * Store database configuration
     *
     * @param string $host
     * @param array $configs
     * @return void
     */
    public static function storeDatabaseConfig(string $host, array $configs): void
    {
        self::storeConfig(
            $host,
            $configs,
            'database',
            'database',
            ['db_password'] // Encrypt sensitive database credentials
        );
    }

    /**
     * Get all configuration types
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return [
            'app' => 'Application',
            'database' => 'Database',
            'mail' => 'Mail',
            'security' => 'Security',
            'cache' => 'Cache',
            'queue' => 'Queue'
        ];
    }

    /**
     * Get all configuration groups
     *
     * @return array
     */
    public static function getGroups(): array
    {
        return [
            'database' => 'Database',
            'security' => 'Security',
            'mail' => 'Mail',
            'general' => 'General',
            'advanced' => 'Advanced'
        ];
    }
} 