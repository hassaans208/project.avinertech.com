<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    public function capabilities(Request $request)
    {
        try {
            $version = DB::connection('ui_api')->select('SELECT VERSION() as version')[0]->version;
            $versionNumber = $this->extractVersionNumber($version);
            
            return response()->json([
                'status' => 'success',
                'message' => __('messages.capabilities_retrieved_successfully'),
                'data' => [
                    'version' => $version,
                    'version_number' => $versionNumber,
                    'capabilities' => [
                        'functional_indexes' => $versionNumber >= 80013,
                        'check_constraints_enforced' => $versionNumber >= 80000,
                        'invisible_indexes' => $versionNumber >= 80000,
                        'generated_columns' => $versionNumber >= 57000,
                        'json_type' => $versionNumber >= 57000,
                        'spatial_indexes' => true, // InnoDB supports this
                        'partitioning' => true,
                        'deprecated_display_widths' => $versionNumber < 80000,
                        'zerofill_attribute' => $versionNumber < 80000,
                    ],
                    'limits' => [
                        'max_index_length' => 3072, // utf8mb4 bytes
                        'max_varchar_length' => 65535,
                        'max_decimal_precision' => 65,
                        'max_decimal_scale' => 30,
                        'max_enum_values' => 65535,
                    ],
                    'engines' => $this->getAvailableEngines(),
                ],
                'timestamp' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID', uniqid())
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.capability_probe_failed'),
                'error' => [
                    'code' => 'CAPABILITY_PROBE_FAILED',
                    'details' => $e->getMessage()
                ],
                'timestamp' => now()->toISOString(),
                'request_id' => $request->header('X-Request-ID', uniqid())
            ], 500);
        }
    }

    public function previewSql(Request $request)
    {
        // This endpoint never executes SQL, only validates and returns preview
        $sql = $request->input('sql');
        
        if (!$sql) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.sql_required'),
                'error' => [
                    'code' => 'SQL_REQUIRED',
                    'details' => 'SQL query is required'
                ]
            ], 400);
        }

        // Basic SQL validation
        $sqlUpper = strtoupper(trim($sql));
        
        // Check for dangerous operations
        $dangerousOps = ['DROP DATABASE', 'DROP TABLE', 'GRANT', 'REVOKE'];
        foreach ($dangerousOps as $op) {
            if (strpos($sqlUpper, $op) !== false) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.dangerous_operation_detected'),
                    'error' => [
                        'code' => 'DANGEROUS_OPERATION',
                        'details' => "Operation '{$op}' is not allowed"
                    ]
                ], 403);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => __('messages.sql_preview_generated'),
            'data' => [
                'sql' => $sql,
                'valid' => true,
                'preview_only' => true
            ]
        ], 200);
    }

    private function extractVersionNumber(string $version): int
    {
        preg_match('/(\d+)\.(\d+)\.(\d+)/', $version, $matches);
        return (int)($matches[1] . str_pad($matches[2], 2, '0', STR_PAD_LEFT) . str_pad($matches[3], 2, '0', STR_PAD_LEFT));
    }

    private function getAvailableEngines(): array
    {
        try {
            $engines = DB::connection('ui_api')->select('SHOW ENGINES');
            return array_map(fn($engine) => $engine->Engine, $engines);
        } catch (\Exception $e) {
            return ['InnoDB']; // Default fallback
        }
    }
}
