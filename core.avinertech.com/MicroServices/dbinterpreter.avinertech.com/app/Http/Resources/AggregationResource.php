<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AggregationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource['name'],
            'label' => $this->resource['label'],
            'description' => $this->resource['description'],
            'syntax' => $this->resource['syntax'],
            'supports_group_by' => $this->resource['supports_group_by'],
            'numeric_only' => $this->resource['numeric_only'] ?? false
        ];
    }
}
