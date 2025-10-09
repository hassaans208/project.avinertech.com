<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\EncryptionHelper;

class TenantDatabase extends Model
{
    use HasFactory;

    protected $table = 'tenant_has_databases';

    protected $fillable = [
        'tenant_id',
        'schema_name',
        'database_details',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns this database
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get decrypted database details
     */
    public function getDecryptedDatabaseDetailsAttribute(): ?array
    {
        try {
            $decrypted = EncryptionHelper::decryptAlphaNumeric($this->database_details);
            if ($decrypted === false) {
                return null;
            }
            
            $data = json_decode($decrypted, true);
            return json_last_error() === JSON_ERROR_NONE ? $data : null;
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt database details: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set encrypted database details
     */
    public function setDatabaseDetailsAttribute(array $details): void
    {
        try {
            $jsonString = json_encode($details);
            $encrypted = EncryptionHelper::encryptAlphaNumeric($jsonString);
            $this->attributes['database_details'] = $encrypted;
        } catch (\Exception $e) {
            \Log::error('Failed to encrypt database details: ' . $e->getMessage());
            throw new \Exception('Failed to encrypt database details');
        }
    }

    /**
     * Get database name from decrypted details
     */
    public function getDatabaseNameAttribute(): ?string
    {
        $details = $this->getDecryptedDatabaseDetailsAttribute();
        return $details['database_name'] ?? null;
    }

    /**
     * Get database host from decrypted details
     */
    public function getDatabaseHostAttribute(): ?string
    {
        $details = $this->getDecryptedDatabaseDetailsAttribute();
        return $details['database_host'] ?? null;
    }

    /**
     * Get database port from decrypted details
     */
    public function getDatabasePortAttribute(): ?int
    {
        $details = $this->getDecryptedDatabaseDetailsAttribute();
        return $details['database_port'] ?? null;
    }

    /**
     * Get database username from decrypted details
     */
    public function getDatabaseUsernameAttribute(): ?string
    {
        $details = $this->getDecryptedDatabaseDetailsAttribute();
        return $details['database_username'] ?? null;
    }

    /**
     * Check if database is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Scope to get active databases
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get databases by schema name
     */
    public function scopeBySchemaName($query, string $schemaName)
    {
        return $query->where('schema_name', $schemaName);
    }

    /**
     * Create database details for a tenant
     */
    public static function createForTenant(int $tenantId, string $schemaName, string $databaseDetails): self
    {
        return self::create([
            'tenant_id' => $tenantId,
            'schema_name' => $schemaName,
            'database_details' => $databaseDetails,
            'is_active' => true,
        ]);
    }
}
