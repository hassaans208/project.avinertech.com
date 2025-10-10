<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchExecutionService
{
    public function executeBatch(int $groupId): array
    {
        try {
            // Get group and operations
            $group = DB::table('operation_groups')->where('id', $groupId)->first();
            if (!$group) {
                throw new \Exception("Operation group {$groupId} not found");
            }

            if ($group->status !== 'approved') {
                throw new \Exception("Operation group {$groupId} is not approved for execution");
            }

            // Update group status to running
            DB::table('operation_groups')
                ->where('id', $groupId)
                ->update([
                    'status' => 'running',
                    'started_at' => now(),
                    'updated_at' => now()
                ]);

            // Get operations in execution order
            $operations = DB::table('operations')
                ->where('group_id', $groupId)
                ->orderBy('execution_order', 'asc')
                ->get();

            $results = [
                'group_id' => $groupId,
                'total_operations' => count($operations),
                'successful_operations' => 0,
                'failed_operations' => 0,
                'operation_results' => []
            ];

            // Execute operations in sequence
            foreach ($operations as $operation) {
                try {
                    $result = $this->executeOperation($operation);
                    $results['successful_operations']++;
                    $results['operation_results'][] = [
                        'operation_id' => $operation->id,
                        'status' => 'success',
                        'result' => $result
                    ];
                } catch (\Exception $e) {
                    $results['failed_operations']++;
                    $results['operation_results'][] = [
                        'operation_id' => $operation->id,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                    // Log the error but continue with next operation
                    Log::error('Operation execution failed', [
                        'operation_id' => $operation->id,
                        'group_id' => $groupId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Update group status based on results
            $groupStatus = $results['failed_operations'] > 0 ? 'failed' : 'completed';
            DB::table('operation_groups')
                ->where('id', $groupId)
                ->update([
                    'status' => $groupStatus,
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            Log::info('Batch execution completed', [
                'group_id' => $groupId,
                'status' => $groupStatus,
                'successful_operations' => $results['successful_operations'],
                'failed_operations' => $results['failed_operations']
            ]);

            return $results;

        } catch (\Exception $e) {
            // Update group status to failed
            DB::table('operation_groups')
                ->where('id', $groupId)
                ->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            Log::error('Batch execution failed', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function executeOperation(object $operation): array
    {
        try {
            // Update operation status to running
            DB::table('operations')
                ->where('id', $operation->id)
                ->update([
                    'status' => 'running',
                    'started_at' => now(),
                    'updated_at' => now()
                ]);

            $payload = json_decode($operation->payload, true);
            $result = null;

            // Execute based on operation type
            switch (strtoupper($operation->type)) {
                case 'CREATE_TABLE':
                    $result = $this->executeCreateTable($operation, $payload);
                    break;
                case 'ALTER_TABLE':
                    $result = $this->executeAlterTable($operation, $payload);
                    break;
                case 'DROP_TABLE':
                    $result = $this->executeDropTable($operation, $payload);
                    break;
                case 'CREATE_INDEX':
                    $result = $this->executeCreateIndex($operation, $payload);
                    break;
                case 'DROP_INDEX':
                    $result = $this->executeDropIndex($operation, $payload);
                    break;
                case 'ADD_FOREIGN_KEY':
                    $result = $this->executeAddForeignKey($operation, $payload);
                    break;
                case 'DROP_FOREIGN_KEY':
                    $result = $this->executeDropForeignKey($operation, $payload);
                    break;
                default:
                    throw new \Exception("Unsupported operation type: {$operation->type}");
            }

            // Update operation status to success
            DB::table('operations')
                ->where('id', $operation->id)
                ->update([
                    'status' => 'success',
                    'result' => json_encode($result),
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            return $result;

        } catch (\Exception $e) {
            // Update operation status to failed
            DB::table('operations')
                ->where('id', $operation->id)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                    'updated_at' => now()
                ]);

            throw $e;
        }
    }

    private function executeCreateTable(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        
        $sql = $this->generateCreateTableSQL($payload, $tableName);
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Table {$tableName} created successfully"
        ];
    }

    private function executeAlterTable(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        $sql = $this->generateAlterTableSQL($payload, $tableName);
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Table {$tableName} altered successfully"
        ];
    }

    private function executeDropTable(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        $sql = "DROP TABLE `{$tableName}`";
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Table {$tableName} dropped successfully"
        ];
    }

    private function executeCreateIndex(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        $sql = $this->generateCreateIndexSQL($payload, $tableName);
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Index created successfully on table {$tableName}"
        ];
    }

    private function executeDropIndex(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        // Handle nested payload structure
        $payloadData = $payload['payload'] ?? $payload;
        $indexName = $payloadData['index_name'] ?? 'unknown';
        $sql = "DROP INDEX `{$indexName}` ON `{$tableName}`";
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Index {$indexName} dropped successfully from table {$tableName}"
        ];
    }

    private function executeAddForeignKey(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        $sql = $this->generateAddForeignKeySQL($payload, $tableName);
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Foreign key added successfully to table {$tableName}"
        ];
    }

    private function executeDropForeignKey(object $operation, array $payload): array
    {
        $tableName = $operation->table_name;
        // Handle nested payload structure
        $payloadData = $payload['payload'] ?? $payload;
        $constraintName = $payloadData['constraint_name'] ?? 'unknown';
        $sql = "ALTER TABLE `{$tableName}` DROP FOREIGN KEY `{$constraintName}`";
        
        DB::connection('ui_api')->statement($sql);

        return [
            'sql_executed' => $sql,
            'table_name' => $tableName,
            'message' => "Foreign key {$constraintName} dropped successfully from table {$tableName}"
        ];
    }

    private function generateCreateTableSQL(array $payload, string $tableName): string
    {
        $sql = "CREATE TABLE `{$tableName}` (\n";
        
        $columns = [];
        $primaryKeys = [];
        
        // Handle nested payload structure
        $columnsData = $payload['payload']['columns'] ?? $payload['columns'] ?? [];
        
        foreach ($columnsData as $column) {
            $colParts = [];
            $colParts[] = "`{$column['name']}`";
            
            // Fix column type - ensure VARCHAR has length
            $columnType = $this->fixColumnType($column['type']);
            $colParts[] = $columnType;
            
            // Handle nullable (default to NOT NULL if not set or false)
            if (array_key_exists('nullable', $column)) {
                if ($column['nullable'] === false || $column['nullable'] === 'NO' || $column['nullable'] === 0) {
                    $colParts[] = "NOT NULL";
                }
            } else {
                $colParts[] = "NOT NULL";
            }
            
            // Handle default
            if (isset($column['default'])) {
                $default = $column['default'];
                if (is_string($default)) {
                    $colParts[] = "DEFAULT '{$default}'";
                } elseif (is_null($default)) {
                    $colParts[] = "DEFAULT NULL";
                } else {
                    $colParts[] = "DEFAULT {$default}";
                }
            }
            
            // Handle auto_increment
            if (!empty($column['auto_increment'])) {
                $colParts[] = "AUTO_INCREMENT";
            }
            
            $columns[] = implode(' ', $colParts);
            
            // Handle primary key
            if (!empty($column['primary_key'])) {
                $primaryKeys[] = "`{$column['name']}`";
            }
        }
        
        // Add primary key constraint if any
        if (!empty($primaryKeys)) {
            $columns[] = "PRIMARY KEY (" . implode(', ', $primaryKeys) . ")";
        }
        
        $sql .= implode(",\n", $columns) . "\n)";
        
        if (isset($payload['engine'])) {
            $sql .= " ENGINE={$payload['engine']}";
        }
        
        return $sql;
    }

    /**
     * Fix column type to ensure proper SQL syntax
     */
    private function fixColumnType(string $type): string
    {
        $type = strtoupper(trim($type));
        
        // Fix VARCHAR without length
        if ($type === 'VARCHAR') {
            return 'VARCHAR(255)';
        }
        
        // Fix CHAR without length
        if ($type === 'CHAR') {
            return 'CHAR(1)';
        }
        
        // Fix DECIMAL without precision/scale
        if ($type === 'DECIMAL') {
            return 'DECIMAL(10,2)';
        }
        
        // Fix FLOAT without precision
        if ($type === 'FLOAT') {
            return 'FLOAT';
        }
        
        // Fix DOUBLE without precision
        if ($type === 'DOUBLE') {
            return 'DOUBLE';
        }
        
        // Fix TEXT types
        if (in_array($type, ['TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'TINYTEXT'])) {
            return $type;
        }
        
        // Fix numeric types
        if (in_array($type, ['INT', 'INTEGER', 'BIGINT', 'SMALLINT', 'TINYINT', 'MEDIUMINT'])) {
            return $type;
        }
        
        // Fix date/time types
        if (in_array($type, ['DATE', 'TIME', 'DATETIME', 'TIMESTAMP', 'YEAR'])) {
            return $type;
        }
        
        // Fix boolean
        if ($type === 'BOOLEAN' || $type === 'BOOL') {
            return 'BOOLEAN';
        }
        
        // Fix JSON
        if ($type === 'JSON') {
            return 'JSON';
        }
        
        // Fix BLOB types
        if (in_array($type, ['BLOB', 'TINYBLOB', 'MEDIUMBLOB', 'LONGBLOB'])) {
            return $type;
        }
        
        // Fix ENUM and SET (these need values, but we'll return as-is for now)
        if (strpos($type, 'ENUM') === 0 || strpos($type, 'SET') === 0) {
            return $type;
        }
        
        // Return as-is if it already has proper format (e.g., VARCHAR(255))
        return $type;
    }

    private function generateAlterTableSQL(array $payload, string $tableName): string
    {
        // Handle nested payload structure
        $payloadData = $payload['payload'] ?? $payload;
        
        if (isset($payloadData['add_column'])) {
            $column = $payloadData['add_column'];
            $columnType = $this->fixColumnType($column['type']);
            $colSql = "`{$column['name']}` {$columnType}";
            if (array_key_exists('nullable', $column)) {
                if ($column['nullable'] === false || $column['nullable'] === 'NO' || $column['nullable'] === 0) {
                    $colSql .= " NOT NULL";
                }
            } else {
                $colSql .= " NOT NULL";
            }
            if (isset($column['default'])) {
                $default = $column['default'];
                if (is_string($default)) {
                    $colSql .= " DEFAULT '{$default}'";
                } elseif (is_null($default)) {
                    $colSql .= " DEFAULT NULL";
                } else {
                    $colSql .= " DEFAULT {$default}";
                }
            }
            if (!empty($column['auto_increment'])) {
                $colSql .= " AUTO_INCREMENT";
            }
            return "ALTER TABLE `{$tableName}` ADD COLUMN {$colSql}";
        }
        
        if (isset($payloadData['modify_column'])) {
            $column = $payloadData['modify_column'];
            $columnType = $this->fixColumnType($column['type']);
            $colSql = "`{$column['name']}` {$columnType}";
            if (array_key_exists('nullable', $column)) {
                if ($column['nullable'] === false || $column['nullable'] === 'NO' || $column['nullable'] === 0) {
                    $colSql .= " NOT NULL";
                }
            } else {
                $colSql .= " NOT NULL";
            }
            if (isset($column['default'])) {
                $default = $column['default'];
                if (is_string($default)) {
                    $colSql .= " DEFAULT '{$default}'";
                } elseif (is_null($default)) {
                    $colSql .= " DEFAULT NULL";
                } else {
                    $colSql .= " DEFAULT {$default}";
                }
            }
            if (!empty($column['auto_increment'])) {
                $colSql .= " AUTO_INCREMENT";
            }
            return "ALTER TABLE `{$tableName}` MODIFY COLUMN {$colSql}";
        }
        
        if (isset($payloadData['drop_column'])) {
            $column = $payloadData['drop_column'];
            return "ALTER TABLE `{$tableName}` DROP COLUMN `{$column['name']}`";
        }
        
        return "ALTER TABLE `{$tableName}`";
    }

    private function generateCreateIndexSQL(array $payload, string $tableName): string
    {
        // Handle nested payload structure
        $payloadData = $payload['payload'] ?? $payload;
        
        $indexName = $payloadData['name'] ?? 'idx_' . $tableName;
        $columns = $payloadData['columns'] ?? [];
        $type = $payloadData['type'] ?? 'INDEX';
        
        $sql = "CREATE {$type} `{$indexName}` ON `{$tableName}` (`" . implode('`, `', $columns) . "`)";
        
        return $sql;
    }

    private function generateAddForeignKeySQL(array $payload, string $tableName): string
    {
        // Handle nested payload structure
        $payloadData = $payload['payload'] ?? $payload;
        
        $constraintName = $payloadData['name'] ?? 'fk_' . $tableName;
        $column = $payloadData['column'];
        $referencedTable = $payloadData['referenced_table'];
        $referencedColumn = $payloadData['referenced_column'];
        
        $sql = "ALTER TABLE `{$tableName}` ADD CONSTRAINT `{$constraintName}` ";
        $sql .= "FOREIGN KEY (`{$column}`) REFERENCES `{$referencedTable}` (`{$referencedColumn}`)";
        
        if (isset($payloadData['on_delete'])) {
            $sql .= " ON DELETE {$payloadData['on_delete']}";
        }
        
        if (isset($payloadData['on_update'])) {
            $sql .= " ON UPDATE {$payloadData['on_update']}";
        }
        
        return $sql;
    }
}
