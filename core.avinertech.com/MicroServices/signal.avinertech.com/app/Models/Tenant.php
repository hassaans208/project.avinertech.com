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
        'status' => 'string',
    ];

    /**
     * Get the packages associated with this tenant.
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'tenant_package')
            ->withPivot('registered_at')
            ->withTimestamps();
    }

    /**
     * Get the signal logs for this tenant.
     */
    public function signalLogs(): HasMany
    {
        return $this->hasMany(SignalLog::class);
    }

    /**
     * Check if tenant is active and not blocked.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    /**
     * Get the current active package for this tenant.
     */
    public function getCurrentPackage(): ?Package
    {
        return $this->packages()
            ->orderByPivot('registered_at', 'desc')
            ->first();
    }

    /**
     * Assign a package to this tenant.
     */
    public function assignPackage(Package $package): void
    {
        $this->packages()->attach($package->id, [
            'registered_at' => now(),
        ]);
    }

    public function createHash(): string
    {
        $package = $this->getCurrentPackage();

        if(empty($package)) {
            $package = Package::getFreePackage();
            $this->assignPackage($package);
        }

        $timestamp = now();
        $hash = encryptAlphaNumeric("{$package->name}:{$timestamp->year}:{$timestamp->month}:{$timestamp->day}:{$timestamp->hour}:{$this->host}");
        return $hash;
    }
} 