<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchemaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Get all schemas accessible to the tenant
            $schemas = DB::connection('ui_api')->select("
                SELECT 
                    SCHEMA_NAME as name,
                    DEFAULT_CHARACTER_SET_NAME as charset,
                    DEFAULT_COLLATION_NAME as collation,
                    SQL_PATH as sql_path
                FROM information_schema.SCHEMATA 
                WHERE SCHEMA_NAME = ?
                ORDER BY SCHEMA_NAME
            ", [$verifiedSchemaName]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.schemas_retrieved_successfully'),
                'data' => $schemas
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve schemas', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.schemas_retrieval_failed'),
                'error' => [
                    'code' => 'SCHEMAS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $verifiedSchemaName = $request->get('schema_name');
            
            // Use verified schema name from signature
            $schemaName = $verifiedSchemaName;
            
            // Get schema details
            $schema = DB::connection('ui_api')->selectOne("
                SELECT 
                    SCHEMA_NAME as name,
                    DEFAULT_CHARACTER_SET_NAME as charset,
                    DEFAULT_COLLATION_NAME as collation,
                    SQL_PATH as sql_path
                FROM information_schema.SCHEMATA 
                WHERE SCHEMA_NAME = ?
            ", [$schemaName]);

            if (!$schema) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.schema_not_found'),
                    'error' => [
                        'code' => 'SCHEMA_NOT_FOUND',
                        'details' => "Schema '{$schemaName}' not found"
                    ]
                ], 404);
            }

            // Get tables in the schema
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
                'message' => __('messages.schema_retrieved_successfully'),
                'data' => [
                    'schema' => $schema,
                    'tables' => $tables,
                    'table_count' => count($tables)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve schema details', [
                'tenant_id' => $tenantId,
                'schema_name' => $schemaName,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.schema_retrieval_failed'),
                'error' => [
                    'code' => 'SCHEMA_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }
}
