<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Tenant extends Model
{
    use SoftDeletes;

    // protected $connection = 'main';
    protected $table = 'Tenant';

    protected $fillable = [
        'tenant_id',
        'host',
        'username',
        'password',
        'port',
        'database_host',
        'database_port',
        'database_name',
        'database_user',
        'database_password',
        'is_active',
        'last_connection_at',
        'connection_log'
    ];

    protected $hidden = [
        'password',
        'database_password'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port' => 'integer',
        'database_port' => 'integer',
        'last_connection_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the connection.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Encrypt password before saving
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt password when retrieving
     */
    public function getPasswordAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Encrypt database password before saving
     */
    public function setDatabasePasswordAttribute($value)
    {
        $this->attributes['database_password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt database password when retrieving
     */
    public function getDatabasePasswordAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

    /**
     * Update last connection timestamp
     */
    public function updateLastConnection()
    {
        $this->update([
            'last_connection_at' => now(),
            'connection_log' => $this->connection_log . "\n" . now() . " - Connection successful"
        ]);
    }

    /**
     * Log connection error
     */
    public function logConnectionError($error)
    {
        $this->update([
            'connection_log' => $this->connection_log . "\n" . now() . " - Error: " . $error
        ]);
    }
} 