<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FilterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->resource['name'],
            'label' => $this->resource['label'],
            'operator' => $this->resource['operator'],
            'description' => $this->resource['description']
        ];
    }
}
