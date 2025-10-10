<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OperationNamingService;
use App\Services\SoftDeleteService;
use App\Services\OperationGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    public function __construct(
        private OperationNamingService $operationNamingService,
        private SoftDeleteService $softDeleteService,
        private OperationGroupService $operationGroupService
    ) {}

    public function index(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $tables = DB::connection('ui_api')->select("
                SELECT 
                    TABLE_NAME as name,
                    TABLE_TYPE as type,
                    ENGINE as engine,
                    TABLE_ROWS as `rows`,
                    AVG_ROW_LENGTH as avg_row_length,
                    DATA_LENGTH as data_length,
                    INDEX_LENGTH as index_length,
                    TABLE_COLLATION as collation,
                    CREATE_TIME as created_at,
                    UPDATE_TIME as updated_at,
                    TABLE_COMMENT as comment
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = ?
                ORDER BY TABLE_NAME
            ", [$schemaName]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.tables_retrieved_successfully'),
                'data' => $tables
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve tables', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.tables_retrieval_failed'),
                'error' => [
                    'code' => 'TABLES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function show(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            // Get table details
            $table = DB::connection('ui_api')->selectOne("
                SELECT 
                    TABLE_NAME as name,
                    TABLE_TYPE as type,
                    ENGINE as engine,
                    TABLE_ROWS as `rows`,
                    AVG_ROW_LENGTH as avg_row_length,
                    DATA_LENGTH as data_length,
                    INDEX_LENGTH as index_length,
                    TABLE_COLLATION as collation,
                    CREATE_TIME as created_at,
                    UPDATE_TIME as updated_at,
                    TABLE_COMMENT as comment
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
            ", [$schemaName, $tableName]);

            if (!$table) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.table_not_found'),
                    'error' => [
                        'code' => 'TABLE_NOT_FOUND',
                        'details' => "Table '{$tableName}' not found in schema '{$schemaName}'"
                    ]
                ], 404);
            }

            // Get columns
            $columns = DB::connection('ui_api')->select("
                SELECT 
                    COLUMN_NAME as name,
                    ORDINAL_POSITION as position,
                    COLUMN_DEFAULT as default_value,
                    IS_NULLABLE as nullable,
                    DATA_TYPE as data_type,
                    CHARACTER_MAXIMUM_LENGTH as max_length,
                    NUMERIC_PRECISION as `precision`,
                    NUMERIC_SCALE as scale,
                    DATETIME_PRECISION as datetime_precision,
                    CHARACTER_SET_NAME as charset,
                    COLLATION_NAME as collation,
                    COLUMN_TYPE as column_type,
                    COLUMN_KEY as `key`,
                    EXTRA as extra,
                    PRIVILEGES as privileges,
                    COLUMN_COMMENT as comment
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION
            ", [$schemaName, $tableName]);

            // Get indexes
            $indexes = DB::connection('ui_api')->select("
                SELECT 
                    INDEX_NAME as name,
                    NON_UNIQUE as non_unique,
                    INDEX_TYPE as type,
                    COLUMN_NAME as column_name,
                    SEQ_IN_INDEX as sequence
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                ORDER BY INDEX_NAME, SEQ_IN_INDEX
            ", [$schemaName, $tableName]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.table_retrieved_successfully'),
                'data' => [
                    'table' => $table,
                    'columns' => $columns,
                    'indexes' => $indexes,
                    'column_count' => count($columns),
                    'index_count' => count($indexes)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve table details', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.table_retrieval_failed'),
                'error' => [
                    'code' => 'TABLE_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $tableData = $request->all();
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'CREATE_TABLE',
                'table_name' => $tableData['name'],
                'case_id' => $tableData['case_id'] ?? 1
            ], $tenantId);

            // Generate SQL preview
            $sqlPreview = $this->generateCreateTableSQL($tableData, $schemaName);

            // Store operation (this would be stored in the operations table)
            // For now, we'll return the preview
            
            return response()->json([
                'status' => 'success',
                'message' => __('messages.table_creation_requested'),
                'data' => [
                    'operation_name' => $operationName,
                    'sql_preview' => $sqlPreview,
                    'status' => 'draft'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create table operation', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.table_creation_failed'),
                'error' => [
                    'code' => 'TABLE_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function getData(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $limit = min($request->get('limit', 100), 1000);
            $offset = $request->get('offset', 0);
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            // Apply query limits based on table structure
            $columnCount = $this->getTableColumnCount($schemaName, $tableName);
            $maxLimit = $columnCount > 3 ? 100 : 1000;
            $limit = min($limit, $maxLimit);

            $query = DB::connection('ui_api')->table("{$schemaName}.{$tableName}");
            
            // Apply filters if provided
            if ($request->has('filters')) {
                $filters = $request->get('filters');
                foreach ($filters as $filter) {
                    $this->applyFilter($query, $filter);
                }
            }

            $results = $query->limit($limit)->offset($offset)->get();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.data_retrieved_successfully'),
                'data' => [
                    'results' => $results,
                    'count' => count($results),
                    'limit' => $limit,
                    'offset' => $offset,
                    'limited' => count($results) >= $maxLimit
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve table data', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.data_retrieval_failed'),
                'error' => [
                    'code' => 'DATA_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function insertData(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $data = $request->get('data');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $id = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")->insertGetId($data);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.data_inserted_successfully'),
                'data' => [
                    'id' => $id,
                    'inserted_data' => $data
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to insert data', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.data_insertion_failed'),
                'error' => [
                    'code' => 'DATA_INSERTION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function updateData(Request $request, string $tableName, string $rowId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $data = $request->get('data');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $updated = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->where('id', $rowId)
                ->update($data);

            if ($updated === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.record_not_found'),
                    'error' => [
                        'code' => 'RECORD_NOT_FOUND',
                        'details' => "Record with ID {$rowId} not found"
                    ]
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.data_updated_successfully'),
                'data' => [
                    'id' => $rowId,
                    'updated_data' => $data
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to update data', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'row_id' => $rowId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.data_update_failed'),
                'error' => [
                    'code' => 'DATA_UPDATE_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function deleteData(Request $request, string $tableName, string $rowId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            // Get original data before soft delete
            $originalData = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->where('id', $rowId)
                ->first();

            if (!$originalData) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.record_not_found'),
                    'error' => [
                        'code' => 'RECORD_NOT_FOUND',
                        'details' => "Record with ID {$rowId} not found"
                    ]
                ], 404);
            }

            // Perform soft delete
            DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->where('id', $rowId)
                ->update([
                    'deleted_at' => now(),
                    'deleted_by' => $tenantId
                ]);

            // Log soft delete
            $this->softDeleteService->logSoftDelete(
                $tenantId,
                $tableName,
                $rowId,
                $originalData,
                $request->header('X-APP-SIGNATURE')
            );

            return response()->json([
                'status' => 'success',
                'message' => __('messages.data_deleted_successfully'),
                'data' => [
                    'id' => $rowId,
                    'soft_deleted' => true
                ]
            ], 204);

        } catch (\Exception $e) {
            Log::error('Failed to delete data', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'row_id' => $rowId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.data_deletion_failed'),
                'error' => [
                    'code' => 'DATA_DELETION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function getSoftDeleted(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $softDeleted = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->whereNotNull('deleted_at')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.soft_deleted_retrieved_successfully'),
                'data' => $softDeleted
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve soft deleted records', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.soft_deleted_retrieval_failed'),
                'error' => [
                    'code' => 'SOFT_DELETED_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function recoverRecord(Request $request, string $tableName, string $recordId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $recovered = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->where('id', $recordId)
                ->whereNotNull('deleted_at')
                ->update([
                    'deleted_at' => null,
                    'deleted_by' => null
                ]);

            if ($recovered === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.record_not_found_or_not_deleted'),
                    'error' => [
                        'code' => 'RECORD_NOT_FOUND_OR_NOT_DELETED',
                        'details' => "Record with ID {$recordId} not found or not soft deleted"
                    ]
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.record_recovered_successfully'),
                'data' => [
                    'id' => $recordId,
                    'recovered' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to recover record', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.record_recovery_failed'),
                'error' => [
                    'code' => 'RECORD_RECOVERY_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function permanentlyDeleteRecord(Request $request, string $tableName, string $recordId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            $deleted = DB::connection('ui_api')->table("{$schemaName}.{$tableName}")
                ->where('id', $recordId)
                ->whereNotNull('deleted_at')
                ->delete();

            if ($deleted === 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.record_not_found_or_not_deleted'),
                    'error' => [
                        'code' => 'RECORD_NOT_FOUND_OR_NOT_DELETED',
                        'details' => "Record with ID {$recordId} not found or not soft deleted"
                    ]
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.record_permanently_deleted_successfully'),
                'data' => [
                    'id' => $recordId,
                    'permanently_deleted' => true
                ]
            ], 204);

        } catch (\Exception $e) {
            Log::error('Failed to permanently delete record', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.record_permanent_deletion_failed'),
                'error' => [
                    'code' => 'RECORD_PERMANENT_DELETION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    // Batch operations - DDL operations that require approval
    public function addColumn(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $schemaName = $verifiedSchemaName;
            
            $request->validate([
                'column' => 'required|array',
                'column.name' => 'required|string',
                'column.type' => 'required|string',
                'case_id' => 'required|integer|exists:operation_cases,id'
            ]);

            $columnData = $request->get('column');
            $caseId = $request->get('case_id');
            
            // Create operation group if it doesn't exist
            $groupId = $this->getOrCreateOperationGroup($tenantId, $caseId, $tableName, 'ALTER_TABLE');
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'ALTER_TABLE',
                'table_name' => $tableName,
                'case_id' => $caseId
            ], $tenantId);
            
            // Create operation
            $operationId = $this->operationGroupService->addOperationToGroup($groupId, [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'type' => 'ALTER_TABLE',
                'name' => $operationName,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'payload' => [
                    'add_column' => $columnData
                ],
                'sql_preview' => "ALTER TABLE `{$tableName}` ADD COLUMN `{$columnData['name']}` {$columnData['type']}"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_added_to_batch'),
                'data' => [
                    'operation_id' => $operationId,
                    'group_id' => $groupId,
                    'operation_name' => $operationName,
                    'status' => 'draft',
                    'sql_preview' => "ALTER TABLE `{$tableName}` ADD COLUMN `{$columnData['name']}` {$columnData['type']}"
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add column operation', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_creation_failed'),
                'error' => [
                    'code' => 'OPERATION_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function updateColumn(Request $request, string $tableName, string $columnName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $schemaName = $verifiedSchemaName;
            
            $request->validate([
                'column' => 'required|array',
                'column.type' => 'required|string',
                'case_id' => 'required|integer|exists:operation_cases,id'
            ]);

            $columnData = $request->get('column');
            $caseId = $request->get('case_id');
            
            // Create operation group if it doesn't exist
            $groupId = $this->getOrCreateOperationGroup($tenantId, $caseId, $tableName, 'ALTER_TABLE');
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'ALTER_TABLE',
                'table_name' => $tableName,
                'case_id' => $caseId
            ], $tenantId);
            
            // Create operation
            $operationId = $this->operationGroupService->addOperationToGroup($groupId, [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'type' => 'ALTER_TABLE',
                'name' => $operationName,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'payload' => [
                    'modify_column' => array_merge($columnData, ['name' => $columnName])
                ],
                'sql_preview' => "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$columnName}` {$columnData['type']}"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_added_to_batch'),
                'data' => [
                    'operation_id' => $operationId,
                    'group_id' => $groupId,
                    'operation_name' => $operationName,
                    'status' => 'draft',
                    'sql_preview' => "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$columnName}` {$columnData['type']}"
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to update column operation', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'column_name' => $columnName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_creation_failed'),
                'error' => [
                    'code' => 'OPERATION_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function deleteColumn(Request $request, string $tableName, string $columnName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $schemaName = $verifiedSchemaName;
            
            $request->validate([
                'case_id' => 'required|integer|exists:operation_cases,id'
            ]);

            $caseId = $request->get('case_id');
            
            // Create operation group if it doesn't exist
            $groupId = $this->getOrCreateOperationGroup($tenantId, $caseId, $tableName, 'ALTER_TABLE');
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'ALTER_TABLE',
                'table_name' => $tableName,
                'case_id' => $caseId
            ], $tenantId);
            
            // Create operation
            $operationId = $this->operationGroupService->addOperationToGroup($groupId, [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'type' => 'ALTER_TABLE',
                'name' => $operationName,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'payload' => [
                    'drop_column' => ['name' => $columnName]
                ],
                'sql_preview' => "ALTER TABLE `{$tableName}` DROP COLUMN `{$columnName}`"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_added_to_batch'),
                'data' => [
                    'operation_id' => $operationId,
                    'group_id' => $groupId,
                    'operation_name' => $operationName,
                    'status' => 'draft',
                    'sql_preview' => "ALTER TABLE `{$tableName}` DROP COLUMN `{$columnName}`"
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to delete column operation', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'column_name' => $columnName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_creation_failed'),
                'error' => [
                    'code' => 'OPERATION_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function addIndex(Request $request, string $tableName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $schemaName = $verifiedSchemaName;
            
            $request->validate([
                'index' => 'required|array',
                'index.name' => 'required|string',
                'index.columns' => 'required|array',
                'index.type' => 'string|in:INDEX,UNIQUE,FULLTEXT,SPATIAL',
                'case_id' => 'required|integer|exists:operation_cases,id'
            ]);

            $indexData = $request->get('index');
            $caseId = $request->get('case_id');
            
            // Create operation group if it doesn't exist
            $groupId = $this->getOrCreateOperationGroup($tenantId, $caseId, $tableName, 'CREATE_INDEX');
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'CREATE_INDEX',
                'table_name' => $tableName,
                'case_id' => $caseId
            ], $tenantId);
            
            // Create operation
            $operationId = $this->operationGroupService->addOperationToGroup($groupId, [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'type' => 'CREATE_INDEX',
                'name' => $operationName,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'payload' => $indexData,
                'sql_preview' => "CREATE {$indexData['type']} `{$indexData['name']}` ON `{$tableName}` (`" . implode('`, `', $indexData['columns']) . "`)"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_added_to_batch'),
                'data' => [
                    'operation_id' => $operationId,
                    'group_id' => $groupId,
                    'operation_name' => $operationName,
                    'status' => 'draft',
                    'sql_preview' => "CREATE {$indexData['type']} `{$indexData['name']}` ON `{$tableName}` (`" . implode('`, `', $indexData['columns']) . "`)"
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to add index operation', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_creation_failed'),
                'error' => [
                    'code' => 'OPERATION_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function deleteIndex(Request $request, string $tableName, string $indexName)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            $schemaName = $verifiedSchemaName;
            
            $request->validate([
                'case_id' => 'required|integer|exists:operation_cases,id'
            ]);

            $caseId = $request->get('case_id');
            
            // Create operation group if it doesn't exist
            $groupId = $this->getOrCreateOperationGroup($tenantId, $caseId, $tableName, 'DROP_INDEX');
            
            // Generate operation name
            $operationName = $this->operationNamingService->generateName([
                'type' => 'DROP_INDEX',
                'table_name' => $tableName,
                'case_id' => $caseId
            ], $tenantId);
            
            // Create operation
            $operationId = $this->operationGroupService->addOperationToGroup($groupId, [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'type' => 'DROP_INDEX',
                'name' => $operationName,
                'schema_name' => $schemaName,
                'table_name' => $tableName,
                'payload' => [
                    'index_name' => $indexName
                ],
                'sql_preview' => "DROP INDEX `{$indexName}` ON `{$tableName}`"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_added_to_batch'),
                'data' => [
                    'operation_id' => $operationId,
                    'group_id' => $groupId,
                    'operation_name' => $operationName,
                    'status' => 'draft',
                    'sql_preview' => "DROP INDEX `{$indexName}` ON `{$tableName}`"
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to delete index operation', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'index_name' => $indexName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_creation_failed'),
                'error' => [
                    'code' => 'OPERATION_CREATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    // Placeholder methods for other batch operations (to be implemented similarly)
    public function updateIndex(Request $request, string $tableName, string $indexName) { return $this->placeholderResponse('updateIndex'); }
    public function addForeignKey(Request $request, string $tableName) { return $this->placeholderResponse('addForeignKey'); }
    public function updateForeignKey(Request $request, string $tableName, string $constraintName) { return $this->placeholderResponse('updateForeignKey'); }
    public function deleteForeignKey(Request $request, string $tableName, string $constraintName) { return $this->placeholderResponse('deleteForeignKey'); }
    public function addCheck(Request $request, string $tableName) { return $this->placeholderResponse('addCheck'); }
    public function updateCheck(Request $request, string $tableName, string $constraintName) { return $this->placeholderResponse('updateCheck'); }
    public function deleteCheck(Request $request, string $tableName, string $constraintName) { return $this->placeholderResponse('deleteCheck'); }
    public function enablePartitioning(Request $request, string $tableName) { return $this->placeholderResponse('enablePartitioning'); }
    public function addPartition(Request $request, string $tableName) { return $this->placeholderResponse('addPartition'); }
    public function reorganizePartitions(Request $request, string $tableName) { return $this->placeholderResponse('reorganizePartitions'); }
    public function update(Request $request, string $tableName) { return $this->placeholderResponse('update'); }

    private function getOrCreateOperationGroup(string $tenantId, int $caseId, string $tableName, string $operationType): int
    {
        // Check if there's already a draft group for this tenant and case
        $existingGroup = DB::table('operation_groups')
            ->where('tenant_id', $tenantId)
            ->where('case_id', $caseId)
            ->where('status', 'draft')
            ->first();

        if ($existingGroup) {
            return $existingGroup->id;
        }

        // Create new group
        $groupName = "BATCH_" . strtoupper($operationType) . "_" . strtoupper($tableName) . "_" . now()->format('YmdHis');
        
        return $this->operationGroupService->createGroup(
            $tenantId,
            $caseId,
            $groupName,
            "Batch operations for {$operationType} on table {$tableName}"
        );
    }

    private function placeholderResponse(string $operation)
    {
        return response()->json([
            'status' => 'success',
            'message' => __('messages.operation_stored_for_batch_processing'),
            'data' => [
                'operation' => $operation,
                'status' => 'draft',
                'note' => 'This operation will be processed in batch after admin approval'
            ]
        ], 201);
    }

    private function generateCreateTableSQL(array $tableData, string $schemaName): string
    {
        $sql = "CREATE TABLE `{$schemaName}`.`{$tableData['name']}` (\n";
        
        $columns = [];
        foreach ($tableData['columns'] as $column) {
            $columnSql = "  `{$column['name']}` {$column['type']}";
            
            if (!empty($column['nullable']) && $column['nullable'] == 'NO') {
                $columnSql .= " NOT NULL";
            }
            
            if (isset($column['default'])) {
                $columnSql .= " DEFAULT '{$column['default']}'";
            }
            
            if (isset($column['auto_increment']) && $column['auto_increment']) {
                $columnSql .= " AUTO_INCREMENT";
            }
            
            if (isset($column['primary_key']) && $column['primary_key']) {
                $columnSql .= " PRIMARY KEY";
            }
            
            $columns[] = $columnSql;
        }
        
        $sql .= implode(",\n", $columns) . "\n)";
        
        if (isset($tableData['engine'])) {
            $sql .= " ENGINE={$tableData['engine']}";
        }
        
        if (isset($tableData['charset'])) {
            $sql .= " DEFAULT CHARSET={$tableData['charset']}";
        }
        
        return $sql;
    }

    private function getTableColumnCount(string $schemaName, string $tableName): int
    {
        $result = DB::connection('ui_api')->selectOne("
            SELECT COUNT(*) as count 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
        ", [$schemaName, $tableName]);
        
        return $result->count ?? 0;
    }

    private function applyFilter($query, array $filter)
    {
        $column = $filter['column'];
        $operator = $filter['operator'];
        $value = $filter['value'];
        
        switch ($operator) {
            case 'equals':
                $query->where($column, '=', $value);
                break;
            case 'not_equals':
                $query->where($column, '!=', $value);
                break;
            case 'greater_than':
                $query->where($column, '>', $value);
                break;
            case 'less_than':
                $query->where($column, '<', $value);
                break;
            case 'like':
                $query->where($column, 'LIKE', $value);
                break;
            case 'in':
                $query->whereIn($column, is_array($value) ? $value : explode(',', $value));
                break;
            case 'between':
                if (is_array($value) && count($value) === 2) {
                    $query->whereBetween($column, $value);
                }
                break;
            case 'is_null':
                $query->whereNull($column);
                break;
            case 'is_not_null':
                $query->whereNotNull($column);
                break;
        }
    }
}
