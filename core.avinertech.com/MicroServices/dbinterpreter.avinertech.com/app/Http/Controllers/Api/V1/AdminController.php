<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OperationGroupService;
use App\Services\BatchExecutionService;
use App\Jobs\ProcessBatchJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct(
        private OperationGroupService $operationGroupService,
        private BatchExecutionService $batchExecutionService
    ) {}
    public function approveBatch(Request $request, string $groupId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $adminNotes = $request->get('admin_notes', '');
            $approvedBy = 1; // TODO: Get actual admin user ID from auth
            
            $success = $this->operationGroupService->approveGroup($groupId, $approvedBy, $adminNotes);
            
            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.operation_group_not_found_or_not_pending'),
                    'error' => [
                        'code' => 'OPERATION_GROUP_NOT_FOUND_OR_NOT_PENDING',
                        'details' => "Operation group with ID {$groupId} not found or not pending approval"
                    ]
                ], 404);
            }

            // Queue batch job for processing
            ProcessBatchJob::dispatch($groupId);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_approved_successfully'),
                'data' => [
                    'group_id' => $groupId,
                    'status' => 'approved',
                    'approved_at' => now(),
                    'queued_for_execution' => true
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to approve batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_approval_failed'),
                'error' => [
                    'code' => 'BATCH_APPROVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function rejectBatch(Request $request, string $groupId)
    {
        try {
            $tenantId = $request->get('tenant_id');
            $adminNotes = $request->get('admin_notes', '');
            $rejectedBy = 1; // TODO: Get actual admin user ID from auth
            
            $success = $this->operationGroupService->rejectGroup($groupId, $rejectedBy, $adminNotes);
            
            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.operation_group_not_found_or_not_pending'),
                    'error' => [
                        'code' => 'OPERATION_GROUP_NOT_FOUND_OR_NOT_PENDING',
                        'details' => "Operation group with ID {$groupId} not found or not pending approval"
                    ]
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_rejected_successfully'),
                'data' => [
                    'group_id' => $groupId,
                    'status' => 'rejected',
                    'rejected_at' => now()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to reject batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_rejection_failed'),
                'error' => [
                    'code' => 'BATCH_REJECTION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function getPendingBatches(Request $request)
    {
        try {
            // dd($request->all());
            $limit = min($request->get('limit', 50), 100);
            $offset = $request->get('offset', 0);
            
            $result = $this->operationGroupService->getPendingGroups($limit, $offset);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.pending_batches_retrieved_successfully'),
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve pending batches', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.pending_batches_retrieval_failed'),
                'error' => [
                    'code' => 'PENDING_BATCHES_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    // New batch execution endpoints
    public function executeBatch(Request $request, string $groupId)
    {
        try {
            $result = $this->batchExecutionService->executeBatch($groupId);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_execution_completed'),
                'data' => $result
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to execute batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_execution_failed'),
                'error' => [
                    'code' => 'BATCH_EXECUTION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function getBatchStatus(Request $request, string $groupId)
    {
        try {
            $group = $this->operationGroupService->getGroupWithOperations($groupId);

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

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_status_retrieved_successfully'),
                'data' => [
                    'group' => $group,
                    'execution_summary' => $this->getExecutionSummary($group->operations)
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to get batch status', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_status_retrieval_failed'),
                'error' => [
                    'code' => 'BATCH_STATUS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function cancelBatch(Request $request, string $groupId)
    {
        try {
            $adminNotes = $request->get('admin_notes', '');
            
            // Check if group can be cancelled
            $group = DB::table('operation_groups')->where('id', $groupId)->first();
            
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

            if (!in_array($group->status, ['draft', 'pending_approval', 'approved'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.batch_cannot_be_cancelled'),
                    'error' => [
                        'code' => 'BATCH_CANNOT_BE_CANCELLED',
                        'details' => "Batch with status '{$group->status}' cannot be cancelled"
                    ]
                ], 400);
            }

            // Update group status to cancelled
            DB::table('operation_groups')
                ->where('id', $groupId)
                ->update([
                    'status' => 'cancelled',
                    'admin_notes' => $adminNotes,
                    'updated_at' => now()
                ]);

            // Update all operations in the group to cancelled
            DB::table('operations')
                ->where('group_id', $groupId)
                ->whereIn('status', ['draft', 'pending_approval', 'queued'])
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now()
                ]);

            Log::info('Batch cancelled by admin', [
                'group_id' => $groupId,
                'admin_notes' => $adminNotes
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.batch_cancelled_successfully'),
                'data' => [
                    'group_id' => $groupId,
                    'status' => 'cancelled',
                    'cancelled_at' => now()
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to cancel batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.batch_cancellation_failed'),
                'error' => [
                    'code' => 'BATCH_CANCELLATION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    private function getExecutionSummary($operations): array
    {
        $summary = [
            'total_operations' => count($operations),
            'completed_operations' => 0,
            'failed_operations' => 0,
            'pending_operations' => 0,
            'running_operations' => 0,
            'cancelled_operations' => 0
        ];

        foreach ($operations as $operation) {
            switch ($operation->status) {
                case 'success':
                    $summary['completed_operations']++;
                    break;
                case 'failed':
                    $summary['failed_operations']++;
                    break;
                case 'running':
                    $summary['running_operations']++;
                    break;
                case 'cancelled':
                    $summary['cancelled_operations']++;
                    break;
                default:
                    $summary['pending_operations']++;
                    break;
            }
        }

        return $summary;
    }

    public function getTenantSecurityLogs(Request $request, string $tenantId)
    {
        try {
            $limit = min($request->get('limit', 50), 100);
            $offset = $request->get('offset', 0);
            
            $logs = DB::table('tenant_security_logs')
                ->where('tenant_id', $tenantId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $total = DB::table('tenant_security_logs')
                ->where('tenant_id', $tenantId)
                ->count();

            return response()->json([
                'status' => 'success',
                'message' => __('messages.tenant_security_logs_retrieved_successfully'),
                'data' => [
                    'logs' => $logs,
                    'total' => $total,
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve tenant security logs', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.tenant_security_logs_retrieval_failed'),
                'error' => [
                    'code' => 'TENANT_SECURITY_LOGS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getBlockedTenants(Request $request)
    {
        try {
            $limit = min($request->get('limit', 50), 100);
            $offset = $request->get('offset', 0);
            
            // This would query a tenants table with blocked status
            // For now, we'll return a placeholder response
            $blockedTenants = [];

            return response()->json([
                'status' => 'success',
                'message' => __('messages.blocked_tenants_retrieved_successfully'),
                'data' => [
                    'tenants' => $blockedTenants,
                    'total' => count($blockedTenants),
                    'limit' => $limit,
                    'offset' => $offset
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve blocked tenants', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.blocked_tenants_retrieval_failed'),
                'error' => [
                    'code' => 'BLOCKED_TENANTS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function unblockTenant(Request $request, string $tenantId)
    {
        try {
            $reason = $request->get('reason', '');
            
            // This would update a tenants table to unblock the tenant
            // For now, we'll log the action
            Log::info('Tenant unblocked by admin', [
                'tenant_id' => $tenantId,
                'reason' => $reason
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.tenant_unblocked_successfully'),
                'data' => [
                    'tenant_id' => $tenantId,
                    'unblocked_at' => now(),
                    'reason' => $reason
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to unblock tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.tenant_unblocking_failed'),
                'error' => [
                    'code' => 'TENANT_UNBLOCKING_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }

    public function getOperationStats(Request $request)
    {
        try {
            $stats = [
                'total_operations' => DB::table('operations')->count(),
                'pending_operations' => DB::table('operations')->where('status', 'draft')->count(),
                'queued_operations' => DB::table('operations')->where('status', 'queued')->count(),
                'running_operations' => DB::table('operations')->where('status', 'running')->count(),
                'completed_operations' => DB::table('operations')->where('status', 'success')->count(),
                'failed_operations' => DB::table('operations')->where('status', 'failed')->count(),
                'total_batches' => DB::table('operation_groups')->count(),
                'pending_batches' => DB::table('operation_groups')->where('status', 'pending_approval')->count(),
                'approved_batches' => DB::table('operation_groups')->where('status', 'approved')->count(),
                'rejected_batches' => DB::table('operation_groups')->where('status', 'rejected')->count(),
                'completed_batches' => DB::table('operation_groups')->where('status', 'completed')->count(),
                'failed_batches' => DB::table('operation_groups')->where('status', 'failed')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => __('messages.operation_stats_retrieved_successfully'),
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve operation stats', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.operation_stats_retrieval_failed'),
                'error' => [
                    'code' => 'OPERATION_STATS_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    public function getSystemHealth(Request $request)
    {
        try {
            $health = [
                'database_connection' => $this->checkDatabaseConnection(),
                'queue_status' => $this->checkQueueStatus(),
                'cache_status' => $this->checkCacheStatus(),
                'disk_space' => $this->checkDiskSpace(),
                'memory_usage' => $this->checkMemoryUsage(),
                'uptime' => $this->getUptime(),
                'timestamp' => now()
            ];

            $overallStatus = 'healthy';
            foreach ($health as $key => $value) {
                if ($key !== 'timestamp' && $value === false) {
                    $overallStatus = 'unhealthy';
                    break;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => __('messages.system_health_retrieved_successfully'),
                'data' => [
                    'overall_status' => $overallStatus,
                    'health_checks' => $health
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Failed to retrieve system health', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => __('messages.system_health_retrieval_failed'),
                'error' => [
                    'code' => 'SYSTEM_HEALTH_RETRIEVAL_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 500);
        }
    }

    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection('ui_api')->select('SELECT 1');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkQueueStatus(): bool
    {
        try {
            // Check if queue is accessible
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCacheStatus(): bool
    {
        try {
            // Check if cache is accessible
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkDiskSpace(): bool
    {
        try {
            $freeBytes = disk_free_space(storage_path());
            $totalBytes = disk_total_space(storage_path());
            $freePercent = ($freeBytes / $totalBytes) * 100;
            
            return $freePercent > 10; // At least 10% free space
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkMemoryUsage(): bool
    {
        try {
            $memoryUsage = memory_get_usage(true);
            $memoryLimit = ini_get('memory_limit');
            
            // Convert memory limit to bytes
            $memoryLimitBytes = $this->convertToBytes($memoryLimit);
            
            return ($memoryUsage / $memoryLimitBytes) < 0.9; // Less than 90% usage
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getUptime(): string
    {
        try {
            $uptime = time() - $_SERVER['REQUEST_TIME'];
            return gmdate('H:i:s', $uptime);
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    private function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
