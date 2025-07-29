<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'is_active',
        'api_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * User can belong to multiple tenants
     */
    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'user_tenant')
                    ->withPivot(['is_active'])
                    ->withTimestamps();
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'SUPER_ADMIN';
    }

    /**
     * Check if user is tenant admin
     */
    public function isTenantAdmin(): bool
    {
        return $this->user_type === 'TENANT_ADMIN';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if user has access to specific tenant
     */
    public function hasAccessToTenant(int $tenantId): bool
    {
        return $this->tenants()
                    ->wherePivot('tenant_id', $tenantId)
                    ->wherePivot('is_active', true)
                    ->exists();
    }

    /**
     * Get user's active tenants
     */
    public function getActiveTenants()
    {
        return $this->tenants()
                    ->wherePivot('is_active', true)
                    ->get();
    }

    /**
     * Generate API token for user
     */
    public function generateApiToken(): string
    {
        $token = Str::random(80);
        $this->update(['api_token' => $token]);
        return $token;
    }

    /**
     * Revoke API token
     */
    public function revokeApiToken(): void
    {
        $this->update(['api_token' => null]);
    }

    /**
     * Assign user to tenant
     */
    public function assignToTenant(Tenant $tenant, string $role = 'member'): void
    {
        $this->tenants()->syncWithoutDetaching([
            $tenant->id => [
                'role' => $role,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Remove user from tenant
     */
    public function removeFromTenant(Tenant $tenant): void
    {
        $this->tenants()->detach($tenant->id);
    }

    /**
     * Get user's role for specific tenant
     */
    public function getRoleForTenant(int $tenantId): ?string
    {
        $tenant = $this->tenants()
                       ->wherePivot('tenant_id', $tenantId)
                       ->first();
        
        return $tenant ? $tenant->pivot->role : null;
    }
}
