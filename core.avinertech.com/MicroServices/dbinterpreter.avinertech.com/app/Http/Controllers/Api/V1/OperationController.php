<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OperationNamingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationController extends Controller
{
    public function __construct(
        private OperationNamingService $operationNamingService
    ) {}

    public function create(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $operationData = $request->all();
            $schemaName = $request->get('schema_name');
            // Generate operation name
            $operationName = $this->operationNamingService->generateName($operationData, $tenantId);

            $caseId = $operationData['case_id'] ?? 1;
            $case = DB::table('operation_cases')->where('id', $caseId)->first();

            $existingGroup = DB::table('operation_groups')->where('tenant_id', $tenantId)->where('case_id', $caseId)->whereIn('status', ['draft', 'pending_approval'])->first();

            if(!$existingGroup) {
                $existingGroup = DB::table('operation_groups')->insertGetId([
                    'tenant_id' => $tenantId,
                    'case_id' => $caseId,
                    'name' => $case->name,
                    'status' => 'draft',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $existingGroup = $existingGroup->id ?? $existingGroup;
            // Store operation in database
            $operation = DB::table('operations')->insertGetId([
                'tenant_id' => $tenantId,
                'group_id' => $existingGroup,
                'case_id' => $operationData['case_id'] ?? 1,
                'name' => $operationName,
                'type' => $operationData['type'],
                'table_name' => $operationData['table_name'] ?? null,
                'payload' => json_encode($operationData),
                'sql_preview' => $this->generateSQLPreview($operationData),
                'status' => 'draft',
                'schema_name' => $schemaName,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_created_successfully'),
                'data' => [
                    'operation_id' => $operation,
                    'operation_name' => $operationName,
                    'group_id' => $existingGroup,
                    'status' => 'draft'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create operation', [
                'tenant_id' => $tenantId,
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

    public function show(Request $request, string $operationId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            
            $operation = DB::table('operations')
                ->where('id', $operationId)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$operation) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.operation_not_found'),
                    'error' => [
                        'code' => 'OPERATION_NOT_FOUND',
                        'details' => "Operation with ID {$operationId} not found"
                    ]
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_retrieved_successfully'),
                'data' => [
                    'id' => $operation->id,
                    'name' => $operation->name,
                    'type' => $operation->type,
                    'table_name' => $operation->table_name,
                    'status' => $operation->status,
                    'sql_preview' => $operation->sql_preview,
                    'result_message' => $operation->result_message,
                    'result_data' => $operation->result_data ? json_decode($operation->result_data, true) : null,
                    'executed_at' => $operation->executed_at,
                    'created_at' => $operation->created_at,
                    'updated_at' => $operation->updated_at
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve operation', [
                'tenant_id' => $tenantId,
                'operation_id' => $operationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_retrieval_failed'),
                'error' => [
                    'code' => 'OPERATION_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $status = $request->get('status');
            $limit = min($request->get('limit', 50), 100);
            $offset = $request->get('offset', 0);
            
            $query = DB::table('operations')->where('tenant_id', $tenantId);
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $operations = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $total = DB::table('operations')->where('tenant_id', $tenantId)->count();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operations_retrieved_successfully'),
                'data' => [
                    'operations' => $operations,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve operations', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operations_retrieval_failed'),
                'error' => [
                    'code' => 'OPERATIONS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getGroups(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $status = $request->get('status');
            $limit = min($request->get('limit', 50), 100);
            $offset = $request->get('offset', 0);
            
            $query = DB::table('operation_groups')->where('tenant_id', $tenantId);
            
            if ($status) {
                $query->where('status', $status);
            }
            
            $groups = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $total = DB::table('operation_groups')->where('tenant_id', $tenantId)->count();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_groups_retrieved_successfully'),
                'data' => [
                    'groups' => $groups,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve operation groups', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_groups_retrieval_failed'),
                'error' => [
                    'code' => 'OPERATION_GROUPS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getGroup(Request $request, string $groupId)
    {
        try {

            $tenantId = $request->get('tenant_id');
            
            $group = DB::table('operation_groups')
                ->where('id', $groupId)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.operation_group_not_found'),
                    'error' => [
                        'code' => 'OPERATION_GROUP_NOT_FOUND',
                        'details' => "Operation group with ID {$groupId} not found"
                    ]
                ], 404);
            }

            // Get operations in this group
            $operations = DB::table('operations')
                ->where('group_id', $groupId)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_group_retrieved_successfully'),
                'data' => [
                    'group' => $group,
                    'operations' => $operations,
                    'operation_count' => count($operations)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve operation group', [
                'tenant_id' => $tenantId,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_group_retrieval_failed'),
                'error' => [
                    'code' => 'OPERATION_GROUP_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function requestApproval(Request $request, string $groupId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $description = $request->get('description', '');
            
            // Check if group exists and belongs to tenant
            $group = DB::table('operation_groups')
                ->where('id', $groupId)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.operation_group_not_found'),
                    'error' => [
                        'code' => 'OPERATION_GROUP_NOT_FOUND',
                        'details' => "Operation group with ID {$groupId} not found"
                    ]
                ], 404);
            }

            // Update group status to pending approval
            DB::table('operation_groups')
                ->where('id', $groupId)
                ->update([
                    'status' => 'pending_approval',
                    'approval_requested_at' => now(),
                    'updated_at' => now()
                ]);

            // TODO: Send notification to admin
            Log::info('Batch approval requested', [
                'tenant_id' => $tenantId,
                'group_id' => $groupId,
                'description' => $description
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_approval_requested'),
                'data' => [
                    'group_id' => $groupId,
                    'status' => 'pending_approval',
                    'approval_requested_at' => now()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to request batch approval', [
                'tenant_id' => $tenantId,
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_approval_request_failed'),
                'error' => [
                    'code' => 'BATCH_APPROVAL_REQUEST_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    private function generateSQLPreview(array $operationData): string
    {
        $type = $operationData['type'];
        $tableName = $operationData['table_name'] ?? 'unknown';
        
        switch (strtoupper($type)) {
            case 'CREATE_TABLE':
                return $this->generateCreateTableSQL($operationData);
            case 'ALTER_TABLE':
                return $this->generateAlterTableSQL($operationData);
            case 'DROP_TABLE':
                return "DROP TABLE `{$tableName}`";
            case 'CREATE_INDEX':
                return $this->generateCreateIndexSQL($operationData);
            case 'DROP_INDEX':
                return "DROP INDEX `{$operationData['index_name']}` ON `{$tableName}`";
            default:
                return "SQL preview for {$type} operation on {$tableName}";
        }
    }

    private function generateCreateTableSQL(array $operationData): string
    {
        $tableName = $operationData['table_name'];
        $sql = "CREATE TABLE `{$tableName}` (\n";
        
        $columns = [];
        foreach ($operationData['payload']['columns'] ?? [] as $column) {
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
        
        if (isset($operationData['payload']['engine'])) {
            $sql .= " ENGINE={$operationData['payload']['engine']}";
        }
        
        return $sql;
    }

    private function generateAlterTableSQL(array $operationData): string
    {
        $tableName = $operationData['table_name'];
        $payload = $operationData['payload'] ?? [];
        
        if (isset($payload['add_column'])) {
            $column = $payload['add_column'];
            return "ALTER TABLE `{$tableName}` ADD COLUMN `{$column['name']}` {$column['type']}";
        }
        
        if (isset($payload['modify_column'])) {
            $column = $payload['modify_column'];
            return "ALTER TABLE `{$tableName}` MODIFY COLUMN `{$column['name']}` {$column['type']}";
        }
        
        return "ALTER TABLE `{$tableName}`";
    }

    private function generateCreateIndexSQL(array $operationData): string
    {
        $tableName = $operationData['table_name'];
        $payload = $operationData['payload'] ?? [];
        $indexName = $payload['name'] ?? 'idx_' . $tableName;
        $columns = $payload['columns'] ?? [];
        $type = $payload['type'] ?? 'INDEX';
        
        $sql = "CREATE {$type} `{$indexName}` ON `{$tableName}` (`" . implode('`, `', $columns) . "`)";
        
        return $sql;
    }
}
