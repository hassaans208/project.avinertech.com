<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OperationGroupService
{
    public function createGroup(string $tenantId, int $caseId, string $groupName, string $description = ''): int
    {
        try {
            $groupId = DB::table('operation_groups')->insertGetId([
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'name' => $groupName,
                'status' => 'draft',
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Operation group created', [
                'group_id' => $groupId,
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'group_name' => $groupName
            ]);

            return $groupId;
        } catch (\Exception $e) {
            Log::error('Failed to create operation group', [
                'tenant_id' => $tenantId,
                'case_id' => $caseId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function addOperationToGroup(int $groupId, array $operationData): int
    {
        try {
            $operationId = DB::table('operations')->insertGetId([
                'group_id' => $groupId,
                'tenant_id' => $operationData['tenant_id'],
                'case_id' => $operationData['case_id'],
                'type' => $operationData['type'],
                'name' => $operationData['name'],
                'schema_name' => $operationData['schema_name'],
                'table_name' => $operationData['table_name'],
                'payload' => json_encode($operationData['payload']),
                'sql_preview' => $operationData['sql_preview'],
                'status' => 'draft',
                'execution_order' => $this->getNextExecutionOrder($groupId),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Operation added to group', [
                'operation_id' => $operationId,
                'group_id' => $groupId,
                'type' => $operationData['type']
            ]);

            return $operationId;
        } catch (\Exception $e) {
            Log::error('Failed to add operation to group', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function requestApproval(int $groupId, string $description = ''): bool
    {
        try {
            $updated = DB::table('operation_groups')
                ->where('id', $groupId)
                ->where('status', 'draft')
                ->update([
                    'status' => 'pending_approval',
                    'description' => $description,
                    'approval_requested_at' => now(),
                    'updated_at' => now()
                ]);

            if ($updated) {
                Log::info('Batch approval requested', [
                    'group_id' => $groupId,
                    'description' => $description
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to request batch approval', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function approveGroup(int $groupId, int $approvedBy, string $adminNotes = ''): bool
    {
        try {
            $updated = DB::table('operation_groups')
                ->where('id', $groupId)
                ->whereIn('status', ['pending_approval', 'failed', 'rejected'])
                ->update([
                    'status' => 'approved',
                    // 'admin_notes' => $adminNotes,
                    'approved_by' => $approvedBy,
                    'approved_at' => now(),
                    'updated_at' => now()
                ]);

            if ($updated) {
                Log::info('Batch approved by admin', [
                    'group_id' => $groupId,
                    'approved_by' => $approvedBy,
                    'admin_notes' => $adminNotes
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to approve batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function rejectGroup(int $groupId, int $rejectedBy, string $adminNotes = ''): bool
    {
        try {
            $updated = DB::table('operation_groups')
                ->where('id', $groupId)
                ->whereIn('status', ['pending_approval', 'failed'])
                ->update([
                    'status' => 'cancelled',
                    // 'admin_notes' => $adminNotes,
                    'approved_by' => $rejectedBy,
                    'approved_at' => now(),
                    'updated_at' => now()
                ]);

            if ($updated) {
                Log::info('Batch rejected by admin', [
                    'group_id' => $groupId,
                    'rejected_by' => $rejectedBy,
                    'admin_notes' => $adminNotes
                ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to reject batch', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getGroupWithOperations(int $groupId): ?object
    {
        try {
            $group = DB::table('operation_groups')
                ->where('id', $groupId)
                ->first();

            if (!$group) {
                return null;
            }

            $operations = DB::table('operations')
                ->where('group_id', $groupId)
                ->orderBy('execution_order', 'asc')
                ->get();

            $group->operations = $operations;
            $group->operation_count = count($operations);

            return $group;
        } catch (\Exception $e) {
            Log::error('Failed to get group with operations', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getGroupsByTenant(string $tenantId, string $status = null, int $limit = 50, int $offset = 0): array
    {
        try {
            $query = DB::table('operation_groups')
                ->where('tenant_id', $tenantId);

            if ($status) {
                $query->where('status', $status);
            }

            $groups = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $total = DB::table('operation_groups')
                ->where('tenant_id', $tenantId)
                ->when($status, function ($query) use ($status) {
                    return $query->where('status', $status);
                })
                ->count();

            return [
                'groups' => $groups,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get groups by tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function getPendingGroups(int $limit = 50, int $offset = 0): array
    {
        try {
            $groups = DB::table('operation_groups')
                ->whereIn('status', ['pending_approval', 'rejected', 'failed'])
                ->orderBy('approval_requested_at', 'asc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $total = DB::table('operation_groups')
                ->whereIn('status', ['pending_approval', 'rejected', 'failed'])
                ->count();

            return [
                'groups' => $groups,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get pending groups', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function getNextExecutionOrder(int $groupId): int
    {
        $lastOrder = DB::table('operations')
            ->where('group_id', $groupId)
            ->max('execution_order');

        return ($lastOrder ?? 0) + 1;
    }
}
