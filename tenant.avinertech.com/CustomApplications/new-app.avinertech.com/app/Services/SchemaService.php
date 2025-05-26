<?php

namespace App\Services;

use App\Models\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\DatabaseSchema;
class SchemaService
{
    /**
     * Generate MySQL queries from schema data
     *
     * @param array $schemaData
     * @return array
     */
    public function generateMySQLQueries(array $schemaData): array
    {
        $queries = [];
        
        // Create table query
        $queries[] = $this->generateCreateTableQuery($schemaData);

        // Generate indexes
        $queries = array_merge($queries, $this->generateIndexQueries($schemaData));

        return $queries;
    }

    /**
     * Generate CREATE TABLE query
     *
     * @param array $schemaData
     * @return string
     */
    protected function generateCreateTableQuery(array $schemaData): string
    {
        $fields = collect($schemaData['schema'])->map(function ($field) {
            $fieldDef = "`{$field['name']}` " . $this->mapFieldTypeToMySQL($field['type']);
            
            // Add nullable
            $fieldDef .= $field['nullable'] ? ' NULL' : ' NOT NULL';
            
            // Add unique constraint
            if ($field['unique']) {
                $fieldDef .= ' UNIQUE';
            }

            return $fieldDef;
        })->toArray();

        // Add timestamps if not present
        if (!collect($schemaData['schema'])->contains('name', 'created_at')) {
            $fields[] = '`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP';
        }
        if (!collect($schemaData['schema'])->contains('name', 'updated_at')) {
            $fields[] = '`updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
        }

        return "CREATE TABLE IF NOT EXISTS `{$schemaData['table_name']}` (\n" .
               implode(",\n", $fields) .
               "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    }

    /**
     * Generate index queries
     *
     * @param array $schemaData
     * @return array
     */
    protected function generateIndexQueries(array $schemaData): array
    {
        $queries = [];
        $indexedFields = collect($schemaData['schema'])
            ->filter(fn($field) => $field['indexed'])
            ->map(fn($field) => $field['name']);

        if ($indexedFields->isNotEmpty()) {
            $indexName = "idx_{$schemaData['table_name']}_" . $indexedFields->implode('_');
            $indexFields = $indexedFields->map(fn($field) => "`{$field}`")->implode(', ');
            $queries[] = "CREATE INDEX `{$indexName}` ON `{$schemaData['table_name']}` ({$indexFields});";
        }

        return $queries;
    }

    /**
     * Map field type to MySQL data type
     *
     * @param string $type
     * @return string
     */
    protected function mapFieldTypeToMySQL(string $type): string
    {
        $typeMap = [
            // Basic Types
            'string' => 'VARCHAR(255)',
            'integer' => 'INT',
            'float' => 'FLOAT',
            'boolean' => 'BOOLEAN',
            'datetime' => 'DATETIME',
            'date' => 'DATE',
            'time' => 'TIME',
            'timestamp' => 'TIMESTAMP',
            
            // Text Types
            'text' => 'TEXT',
            'longText' => 'LONGTEXT',
            'mediumText' => 'MEDIUMTEXT',
            'char' => 'CHAR(255)',
            
            // Numeric Types
            'decimal' => 'DECIMAL(10,2)',
            'double' => 'DOUBLE',
            'bigInteger' => 'BIGINT',
            'unsignedInteger' => 'INT UNSIGNED',
            'unsignedBigInteger' => 'BIGINT UNSIGNED',
            
            // Special Types
            'json' => 'JSON',
            'jsonb' => 'JSON',
            'binary' => 'BLOB',
            'uuid' => 'CHAR(36)',
            'ipAddress' => 'VARCHAR(45)',
            'macAddress' => 'VARCHAR(17)'
        ];

        return $typeMap[$type] ?? 'VARCHAR(255)';
    }

    /**
     * Store schema data
     *
     * @param array $schemas
     * @return array
     * @throws \Exception
     */
    public function storeSchema(array $schemas): array
    {
        DB::beginTransaction();
        try {
            $storedSchemas = [];
            foreach ($schemas as $schemaData) {
                // Generate MySQL queries
                $queries = $this->generateMySQLQueries($schemaData);

                // Store in database
                $schema = DatabaseSchema::updateOrCreate(
                    [
                        'model_name' => $schemaData['name']
                    ],
                    [
                        'schema' => $schemaData['fields'],
                        'table_type' => $schemaData['tableType'],
                        'description' => $schemaData['description'],
                        'migration' => $schemaData['migration'],
                        'model' => $schemaData['model'],
                        'factory' => $schemaData['factory'],
                        'queries' => array_merge($queries, $schemaData['queries'])
                    ]
                );

                $storedSchemas[] = $schema;
            }

            DB::commit();
            return $storedSchemas;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get all schemas
     *
     * @return array
     */
    public function getSchemas(): array
    {
        return Schema::all()
            ->map(function ($schema) {
                return [
                    'name' => $schema->model_name,
                    'fields' => $schema->schema,
                    'queries' => $schema->queries
                ];
            })
            ->toArray();
    }
} 