<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ColumnResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'schema' => $this->resource->TABLE_SCHEMA,
            'table' => $this->resource->TABLE_NAME,
            'column' => $this->resource->COLUMN_NAME,
            'position' => $this->resource->ORDINAL_POSITION,
            'default' => $this->resource->COLUMN_DEFAULT,
            'nullable' => $this->resource->IS_NULLABLE === 'YES',
            'data_type' => $this->resource->DATA_TYPE,
            'max_length' => $this->resource->CHARACTER_MAXIMUM_LENGTH,
            'precision' => $this->resource->NUMERIC_PRECISION,
            'scale' => $this->resource->NUMERIC_SCALE,
            'datetime_precision' => $this->resource->DATETIME_PRECISION,
            'charset' => $this->resource->CHARACTER_SET_NAME,
            'collation' => $this->resource->COLLATION_NAME,
            'column_type' => $this->resource->COLUMN_TYPE,
            'key' => $this->resource->COLUMN_KEY,
            'extra' => $this->resource->EXTRA,
            'privileges' => $this->resource->PRIVILEGES,
            'comment' => $this->resource->COLUMN_COMMENT,
            'generation_expression' => $this->resource->GENERATION_EXPRESSION,
            // 'srs_id' => $this->resource->SRS_ID
        ];
    }
}
