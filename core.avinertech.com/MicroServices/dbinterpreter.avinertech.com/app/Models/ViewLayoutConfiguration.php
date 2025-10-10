<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViewLayoutConfiguration extends Model
{
    protected $fillable = [
        'view_definition_id',
        'layout_type',
        'layout_config',
        'responsive_config',
        'theme_config'
    ];

    protected $casts = [
        'layout_config' => 'array',
        'responsive_config' => 'array',
        'theme_config' => 'array'
    ];

    public function viewDefinition(): BelongsTo
    {
        return $this->belongsTo(ViewDefinition::class);
    }

    public function scopeByType($query, string $layoutType)
    {
        return $query->where('layout_type', $layoutType);
    }
}
