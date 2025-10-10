<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewPermission extends Model
{
    protected $fillable = [
        'view_definition_id',
        'user_id',
        'role_id',
        'permission_type',
        'granted_by'
    ];

    public function viewDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewDefinition::class);
    }

    public function scopeByPermission($query, string $permissionType)
    {
        return $query->where('permission_type', $permissionType);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForRole($query, int $roleId)
    {
        return $query->where('role_id', $roleId);
    }
}
