<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MetadataService
{
    public function getAllFilterOperators(): array
    {
        return [
            [
                'name' => 'equals',
                'label' => 'Equals',
                'operator' => '=',
                'description' => 'Exact match'
            ],
            [
                'name' => 'not_equals',
                'label' => 'Not Equals',
                'operator' => '!=',
                'description' => 'Not equal to'
            ],
            [
                'name' => 'greater_than',
                'label' => 'Greater Than',
                'operator' => '>',
                'description' => 'Greater than value'
            ],
            [
                'name' => 'greater_than_equal',
                'label' => 'Greater Than or Equal',
                'operator' => '>=',
                'description' => 'Greater than or equal to value'
            ],
            [
                'name' => 'less_than',
                'label' => 'Less Than',
                'operator' => '<',
                'description' => 'Less than value'
            ],
            [
                'name' => 'less_than_equal',
                'label' => 'Less Than or Equal',
                'operator' => '<=',
                'description' => 'Less than or equal to value'
            ],
            [
                'name' => 'like',
                'label' => 'Contains',
                'operator' => 'LIKE',
                'description' => 'Contains text (use % wildcards)'
            ],
            [
                'name' => 'not_like',
                'label' => 'Does Not Contain',
                'operator' => 'NOT LIKE',
                'description' => 'Does not contain text'
            ],
            [
                'name' => 'in',
                'label' => 'In List',
                'operator' => 'IN',
                'description' => 'Value is in list'
            ],
            [
                'name' => 'not_in',
                'label' => 'Not In List',
                'operator' => 'NOT IN',
                'description' => 'Value is not in list'
            ],
            [
                'name' => 'between',
                'label' => 'Between',
                'operator' => 'BETWEEN',
                'description' => 'Value is between two values'
            ],
            [
                'name' => 'not_between',
                'label' => 'Not Between',
                'operator' => 'NOT BETWEEN',
                'description' => 'Value is not between two values'
            ],
            [
                'name' => 'is_null',
                'label' => 'Is Null',
                'operator' => 'IS NULL',
                'description' => 'Value is null'
            ],
            [
                'name' => 'is_not_null',
                'label' => 'Is Not Null',
                'operator' => 'IS NOT NULL',
                'description' => 'Value is not null'
            ],
            [
                'name' => 'starts_with',
                'label' => 'Starts With',
                'operator' => 'LIKE',
                'description' => 'Starts with text'
            ],
            [
                'name' => 'ends_with',
                'label' => 'Ends With',
                'operator' => 'LIKE',
                'description' => 'Ends with text'
            ]
        ];
    }

    public function getAllAggregationFunctions(): array
    {
        return [
            [
                'name' => 'count',
                'label' => 'Count',
                'description' => 'Count number of records',
                'syntax' => 'COUNT(*)',
                'supports_group_by' => true
            ],
            [
                'name' => 'count_distinct',
                'label' => 'Count Distinct',
                'description' => 'Count distinct values',
                'syntax' => 'COUNT(DISTINCT column)',
                'supports_group_by' => true
            ],
            [
                'name' => 'sum',
                'label' => 'Sum',
                'description' => 'Sum of numeric values',
                'syntax' => 'SUM(column)',
                'supports_group_by' => true,
                'numeric_only' => true
            ],
            [
                'name' => 'avg',
                'label' => 'Average',
                'description' => 'Average of numeric values',
                'syntax' => 'AVG(column)',
                'supports_group_by' => true,
                'numeric_only' => true
            ],
            [
                'name' => 'min',
                'label' => 'Minimum',
                'description' => 'Minimum value',
                'syntax' => 'MIN(column)',
                'supports_group_by' => true
            ],
            [
                'name' => 'max',
                'label' => 'Maximum',
                'description' => 'Maximum value',
                'syntax' => 'MAX(column)',
                'supports_group_by' => true
            ],
            [
                'name' => 'group_concat',
                'label' => 'Group Concatenate',
                'description' => 'Concatenate values in group',
                'syntax' => 'GROUP_CONCAT(column)',
                'supports_group_by' => true
            ]
        ];
    }

    public function getAllTenantColumns(string $tenantId, string $schemaName): array
    {
        $columns = DB::connection('ui_api')->select("
            SELECT 
                c.TABLE_SCHEMA,
                c.TABLE_NAME,
                c.COLUMN_NAME,
                c.ORDINAL_POSITION,
                c.COLUMN_DEFAULT,
                c.IS_NULLABLE,
                c.DATA_TYPE,
                c.CHARACTER_MAXIMUM_LENGTH,
                c.NUMERIC_PRECISION,
                c.NUMERIC_SCALE,
                c.DATETIME_PRECISION,
                c.CHARACTER_SET_NAME,
                c.COLLATION_NAME,
                c.COLUMN_TYPE,
                c.COLUMN_KEY,
                c.EXTRA,
                c.PRIVILEGES,
                c.COLUMN_COMMENT,
                c.GENERATION_EXPRESSION
            FROM information_schema.COLUMNS c
            INNER JOIN information_schema.TABLES t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME
            WHERE c.TABLE_SCHEMA = ?
            AND t.TABLE_TYPE = 'BASE TABLE'
            ORDER BY c.TABLE_SCHEMA, c.TABLE_NAME, c.ORDINAL_POSITION
        ", [$schemaName]);

        return $columns;
    }
}
