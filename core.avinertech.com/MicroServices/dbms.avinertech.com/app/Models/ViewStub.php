<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ViewStub extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'model_name',
        'view_path',
        'layout',
        'title',
        'description',
        'fields',
        'actions',
        'relationships',
        'generated_views',
        'encryption_id',
        'is_active'
    ];

    protected $casts = [
        'fields' => 'array',
        'actions' => 'array',
        'relationships' => 'array',
        'generated_views' => 'array',
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getViewPathAttribute($value)
    {
        return $value ?? strtolower($this->model_name);
    }
} 