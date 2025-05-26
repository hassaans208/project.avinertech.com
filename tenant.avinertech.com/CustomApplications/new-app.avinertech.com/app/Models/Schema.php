<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schema extends Model
{
    protected $fillable = [
        'model_name',
        'schema',
        'table_type',
        'queries',
        'app_id'
    ];

    protected $casts = [
        'schema' => 'array',
        'queries' => 'array'
    ];
} 