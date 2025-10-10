<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ViewDefinition extends Model
{
    protected $fillable = [
        'tenant_id',
        'schema_name',
        'table_name',
        'view_name',
        'view_type',
        'title',
        'description',
        'is_active',
        'cache_key',
        'cached_content',
        'cache_expires_at',
        'schema_version',
        'rendering_mode',
        'view_configuration'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cache_expires_at' => 'datetime',
        'schema_version' => 'integer',
        'view_configuration' => 'array'
    ];

    public function columnConfigurations(): HasMany
    {
        return $this->hasMany(ViewColumnConfiguration::class);
    }

    public function layoutConfigurations(): HasMany
    {
        return $this->hasMany(ViewLayoutConfiguration::class);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(ViewPermission::class);
    }

    /**
     * Get the view type definition
     */
    public function viewTypeDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewType::class, 'view_type', 'name');
    }

    /**
     * Get the view configuration with defaults
     */
    public function getViewConfiguration(): array
    {
        $config = $this->view_configuration ?? [];
        
        if ($this->viewTypeDefinition) {
            $defaultConfig = $this->viewTypeDefinition->getDefaultConfiguration();
            $config = array_merge($defaultConfig, $config);
        }
        
        return $config;
    }

    /**
     * Validate view configuration against view type options
     */
    public function validateConfiguration(): array
    {
        if (!$this->viewTypeDefinition) {
            return ['View type not found'];
        }
        
        return $this->viewTypeDefinition->validateConfiguration($this->view_configuration ?? []);
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForTable($query, string $schemaName, string $tableName)
    {
        return $query->where('schema_name', $schemaName)
                    ->where('table_name', $tableName);
    }

    public function scopeByType($query, string $viewType)
    {
        return $query->where('view_type', $viewType);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isCacheValid(): bool
    {
        return $this->cached_content && 
               $this->cache_expires_at && 
               $this->cache_expires_at->isFuture() &&
               $this->isSchemaCurrent();
    }

    public function isSchemaCurrent(): bool
    {
        $currentSchemaVersion = $this->getCurrentSchemaVersion();
        return $this->schema_version === $currentSchemaVersion;
    }

    private function getCurrentSchemaVersion(): int
    {
        $result = DB::connection('ui_api')->selectOne("
            SELECT UNIX_TIMESTAMP(MAX(UPDATE_TIME)) as version
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ", [$this->schema_name, $this->table_name]);
        
        return $result->version ?? 0;
    }

    public function getRenderingMode(): string
    {
        return $this->rendering_mode ?? 'hybrid';
    }

    public function getCacheKey(): string
    {
        return $this->cache_key ?? $this->generateCacheKey();
    }

    private function generateCacheKey(): string
    {
        return "view_{$this->tenant_id}_{$this->schema_name}_{$this->table_name}_{$this->view_name}_{$this->view_type}";
    }
}
