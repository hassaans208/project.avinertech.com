<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SchemaAnalysisService
{
    public function analyzeTable(string $tenantId, string $schemaName, string $tableName): array
    {
        // Get table structure
        $columns = DB::connection('ui_api')->select("
            SELECT 
                COLUMN_NAME,
                DATA_TYPE,
                COLUMN_TYPE,
                IS_NULLABLE,
                COLUMN_DEFAULT,
                COLUMN_KEY,
                EXTRA,
                COLUMN_COMMENT
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY ORDINAL_POSITION
        ", [$schemaName, $tableName]);

        // Get indexes
        $indexes = DB::connection('ui_api')->select("
            SELECT 
                INDEX_NAME,
                COLUMN_NAME,
                NON_UNIQUE,
                INDEX_TYPE
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ORDER BY INDEX_NAME, SEQ_IN_INDEX
        ", [$schemaName, $tableName]);

        // Get foreign keys
        $foreignKeys = DB::connection('ui_api')->select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME,
                UPDATE_RULE,
                DELETE_RULE
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$schemaName, $tableName]);

        return [
            'table_name' => $tableName,
            'schema_name' => $schemaName,
            'columns' => $this->processColumns($columns),
            'indexes' => $this->processIndexes($indexes),
            'foreign_keys' => $this->processForeignKeys($foreignKeys),
            'analyzed_at' => now()
        ];
    }

    private function processColumns(array $columns): array
    {
        return array_map(function ($column) {
            return [
                'name' => $column->COLUMN_NAME,
                'type' => $column->DATA_TYPE,
                'full_type' => $column->COLUMN_TYPE,
                'nullable' => $column->IS_NULLABLE === 'YES',
                'default' => $column->COLUMN_DEFAULT,
                'is_primary_key' => $column->COLUMN_KEY === 'PRI',
                'is_auto_increment' => str_contains($column->EXTRA, 'auto_increment'),
                'comment' => $column->COLUMN_COMMENT,
                'is_password_field' => $this->isPasswordField($column),
                'form_control' => $this->getFormControl($column),
                'validation_rules' => $this->getValidationRules($column),
                'display_name' => $this->generateDisplayName($column->COLUMN_NAME),
                'is_editable' => !$this->isSystemField($column),
                'is_visible' => true,
                'is_searchable' => $this->isSearchable($column),
                'is_sortable' => $this->isSortable($column)
            ];
        }, $columns);
    }

    private function processIndexes(array $indexes): array
    {
        $processedIndexes = [];
        $currentIndex = null;

        foreach ($indexes as $index) {
            if ($currentIndex === null || $currentIndex['name'] !== $index->INDEX_NAME) {
                if ($currentIndex !== null) {
                    $processedIndexes[] = $currentIndex;
                }
                $currentIndex = [
                    'name' => $index->INDEX_NAME,
                    'columns' => [],
                    'is_unique' => $index->NON_UNIQUE == 0,
                    'type' => $index->INDEX_TYPE
                ];
            }
            $currentIndex['columns'][] = $index->COLUMN_NAME;
        }

        if ($currentIndex !== null) {
            $processedIndexes[] = $currentIndex;
        }

        return $processedIndexes;
    }

    private function processForeignKeys(array $foreignKeys): array
    {
        return array_map(function ($fk) {
            return [
                'constraint_name' => $fk->CONSTRAINT_NAME,
                'column_name' => $fk->COLUMN_NAME,
                'referenced_table' => $fk->REFERENCED_TABLE_NAME,
                'referenced_column' => $fk->REFERENCED_COLUMN_NAME,
                'update_rule' => $fk->UPDATE_RULE,
                'delete_rule' => $fk->DELETE_RULE
            ];
        }, $foreignKeys);
    }

    private function isPasswordField($column): bool
    {
        $passwordFields = ['password', 'passwd', 'pwd', 'secret', 'token'];
        return in_array(strtolower($column->COLUMN_NAME), $passwordFields) ||
               str_contains(strtolower($column->COLUMN_TYPE), 'password');
    }

    private function getFormControl($column): string
    {
        $type = strtolower($column->DATA_TYPE);
        
        if ($this->isPasswordField($column)) {
            return 'password';
        }
        
        switch ($type) {
            case 'varchar':
            case 'char':
            case 'text':
                return 'text';
            case 'int':
            case 'bigint':
            case 'decimal':
            case 'float':
            case 'double':
                return 'number';
            case 'date':
                return 'date';
            case 'datetime':
            case 'timestamp':
                return 'datetime';
            case 'time':
                return 'time';
            case 'tinyint':
                return 'checkbox';
            case 'json':
                return 'textarea';
            default:
                return 'text';
        }
    }

    private function getValidationRules($column): array
    {
        $rules = [];
        
        if ($column->IS_NULLABLE === 'NO') {
            $rules['required'] = true;
        }
        
        if ($this->isPasswordField($column)) {
            $rules['min'] = 8;
            $rules['pattern'] = '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)';
        }
        
        if (str_contains($column->COLUMN_NAME, 'email')) {
            $rules['email'] = true;
        }
        
        if (str_contains($column->COLUMN_TYPE, 'varchar')) {
            preg_match('/varchar\((\d+)\)/', $column->COLUMN_TYPE, $matches);
            if (isset($matches[1])) {
                $rules['max'] = (int)$matches[1];
            }
        }
        
        return $rules;
    }

    private function generateDisplayName(string $columnName): string
    {
        return ucwords(str_replace('_', ' ', $columnName));
    }

    private function isSystemField($column): bool
    {
        $systemFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
        return in_array($column->COLUMN_NAME, $systemFields);
    }

    private function isSearchable($column): bool
    {
        $searchableTypes = ['varchar', 'char', 'text', 'int', 'bigint'];
        return in_array(strtolower($column->DATA_TYPE), $searchableTypes) &&
               !$this->isSystemField($column);
    }

    private function isSortable($column): bool
    {
        return !$this->isSystemField($column);
    }
}
