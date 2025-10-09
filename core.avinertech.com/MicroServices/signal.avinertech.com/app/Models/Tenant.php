<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'host',
        'status',
        'block_reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tenant can have many packages
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'tenant_package')
                    ->withPivot('registered_at')
                    ->withTimestamps();
    }

    /**
     * Tenant can have many users
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_tenant')
                    ->withPivot(['role', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Tenant has many signal logs
     */
    public function signalLogs(): HasMany
    {
        return $this->hasMany(SignalLog::class);
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is blocked
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Get current package for tenant
     */
    public function getCurrentPackage(): ?Package
    {
        return $this->packages()
                    ->orderBy('tenant_package.created_at', 'desc')
                    ->first();
    }

    /**
     * Assign package to tenant
     */
    public function assignPackage(Package $package): void
    {
        $this->packages()->attach($package->id, [
            'registered_at' => now(),
        ]);
    }

    /**
     * Create hash for tenant (used in signal processing)
     */
    public function createHash(User $user): string
    {
        $package = $this->getCurrentPackage();

        if(empty($package)) {
            $package = Package::getFreePackage();
            $this->assignPackage($package);
        }

        $timestamp = now();
        return encryptAlphaNumeric(sprintf(
            '%s:%s:%s:%s:%s:%s:%s:%s:%s',
            $package->name,
            $timestamp->format('Y'),
            $timestamp->format('m'),
            $timestamp->format('d'),
            $timestamp->format('H'),
            $this->host,
            (string) $user->id,
            $user->email,
            (string) $package->id
        ));
    }

    /**
     * Get tenant admins
     */
    public function getAdmins()
    {
        return $this->users()
                    ->wherePivot('role', 'admin')
                    ->wherePivot('is_active', true)
                    ->get();
    }

    /**
     * Get tenant members
     */
    public function getMembers()
    {
        return $this->users()
                    ->wherePivot('role', 'member')
                    ->wherePivot('is_active', true)
                    ->get();
    }

    /**
     * Tenant has many databases
     */
    public function databases(): HasMany
    {
        return $this->hasMany(TenantDatabase::class);
    }

    /**
     * Get active databases for tenant
     */
    public function getActiveDatabases()
    {
        return $this->databases()->where('is_active', true)->get();
    }

    /**
     * Get database by schema name
     */
    public function getDatabaseBySchemaName(string $schemaName): ?TenantDatabase
    {
        return $this->databases()
                    ->where('schema_name', $schemaName)
                    ->where('is_active', true)
                    ->first();
    }
} 