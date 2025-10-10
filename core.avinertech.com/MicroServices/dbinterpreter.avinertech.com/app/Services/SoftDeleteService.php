<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SoftDeleteService
{
    public function logSoftDelete(
        string $tenantId,
        string $tableName,
        string $recordId,
        $originalData,
        string $signature
    ): void {
        try {
            DB::table('soft_delete_logs')->insert([
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'original_data' => json_encode($originalData),
                'deleted_by' => $tenantId,
                'deleted_at' => now(),
                'signature' => $signature,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Soft delete logged', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'record_id' => $recordId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log soft delete', [
                'tenant_id' => $tenantId,
                'table_name' => $tableName,
                'record_id' => $recordId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getSoftDeleteLog(string $tenantId, string $tableName, string $recordId)
    {
        return DB::table('soft_delete_logs')
            ->where('tenant_id', $tenantId)
            ->where('table_name', $tableName)
            ->where('record_id', $recordId)
            ->whereNull('recovered_at')
            ->whereNull('permanently_deleted_at')
            ->first();
    }

    public function markAsRecovered(string $tenantId, string $tableName, string $recordId): void
    {
        DB::table('soft_delete_logs')
            ->where('tenant_id', $tenantId)
            ->where('table_name', $tableName)
            ->where('record_id', $recordId)
            ->whereNull('recovered_at')
            ->whereNull('permanently_deleted_at')
            ->update([
                'recovered_at' => now(),
                'updated_at' => now()
            ]);
    }

    public function markAsPermanentlyDeleted(string $tenantId, string $tableName, string $recordId): void
    {
        DB::table('soft_delete_logs')
            ->where('tenant_id', $tenantId)
            ->where('table_name', $tableName)
            ->where('record_id', $recordId)
            ->whereNull('recovered_at')
            ->whereNull('permanently_deleted_at')
            ->update([
                'permanently_deleted_at' => now(),
                'updated_at' => now()
            ]);
    }

    public function getSoftDeletedRecords(string $tenantId, string $tableName = null)
    {
        $query = DB::table('soft_delete_logs')
            ->where('tenant_id', $tenantId)
            ->whereNull('recovered_at')
            ->whereNull('permanently_deleted_at');

        if ($tableName) {
            $query->where('table_name', $tableName);
        }

        return $query->orderBy('deleted_at', 'desc')->get();
    }

    public function cleanupOldSoftDeletes(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        $deleted = DB::table('soft_delete_logs')
            ->where('deleted_at', '<', $cutoffDate)
            ->whereNull('recovered_at')
            ->whereNull('permanently_deleted_at')
            ->update([
                'permanently_deleted_at' => now(),
                'updated_at' => now()
            ]);

        Log::info('Cleaned up old soft deletes', [
            'deleted_count' => $deleted,
            'cutoff_date' => $cutoffDate
        ]);

        return $deleted;
    }
}
