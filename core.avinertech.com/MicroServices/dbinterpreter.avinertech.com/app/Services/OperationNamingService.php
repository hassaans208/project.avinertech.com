<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OperationNamingService
{
    public function generateName(array $requestData, string $tenantId): string
    {
        $operationType = $requestData['type'];
        $tableName = $requestData['table_name'] ?? 'unknown';
        $caseId = $requestData['case_id'];
        
        $case = DB::table('operation_cases')->where('id', $caseId)->first();
        
        if ($case && $case->execution_mode === 'batch') {
            return $this->generateBatchName($operationType, $tableName, $tenantId);
        }
        
        return $this->generateInstantName($operationType, $tableName, $requestData);
    }
    
    private function generateBatchName(string $operationType, string $tableName, string $tenantId): string
    {
        // Get next batch number for tenant
        $batchNumber = $this->getNextBatchNumber($tenantId);
        $paddedBatchNumber = str_pad($batchNumber, 15, '0', STR_PAD_LEFT);
        
        switch (strtoupper($operationType)) {
            case 'CREATE_TABLE':
                return "BATCH{$batchNumber}_CREATE_{$tableName}_TABLE";
            case 'ALTER_TABLE':
                return "BATCH{$batchNumber}_ALTER_{$tableName}_TABLE";
            case 'DROP_TABLE':
                return "BATCH{$batchNumber}_DROP_{$tableName}_TABLE";
            case 'CREATE_INDEX':
                return "BATCH{$batchNumber}_CREATE_{$tableName}_INDEX";
            case 'DROP_INDEX':
                return "BATCH{$batchNumber}_DROP_{$tableName}_INDEX";
            case 'ADD_FOREIGN_KEY':
                return "BATCH{$batchNumber}_ADD_{$tableName}_FK";
            case 'DROP_FOREIGN_KEY':
                return "BATCH{$batchNumber}_DROP_{$tableName}_FK";
            default:
                return "BATCH{$batchNumber}_{$operationType}_{$tableName}";
        }
    }
    
    private function generateInstantName(string $operationType, string $tableName, array $requestData): string
    {
        $recordId = $requestData['record_id'] ?? 1;
        $paddedId = str_pad($recordId, 15, '0', STR_PAD_LEFT);

        switch (strtoupper($operationType)) {
            case 'SELECT':
                $hasFilters = $this->hasFilters($requestData);
                return "INSTANT_{$paddedId}_SELECT_{$tableName}_" . ($hasFilters ? 'WITH' : 'WITHOUT') . "_FILTERS";
            case 'INSERT':
 
                return "INSTANT_{$paddedId}_INSERT_{$tableName}_RECORD";
            case 'UPDATE':
                return "INSTANT_{$paddedId}_UPDATE_{$tableName}_RECORD";
            case 'DELETE':
                return "INSTANT_{$paddedId}_DELETE_{$tableName}_RECORD";
            default:
                return "INSTANT_{$paddedId}_{$operationType}_{$tableName}";
        }
    }
    
    private function getNextBatchNumber(string $tenantId): int
    {
        $lastBatch = DB::table('operation_groups')
            ->where('tenant_id', $tenantId)
            ->where('name', 'like', 'BATCH%')
            ->orderBy('created_at', 'desc')
            ->first();
            
        if (!$lastBatch) {
            return 1;
        }
        
        preg_match('/BATCH(\d+)_/', $lastBatch->name, $matches);
        return (int)($matches[1] ?? 0) + 1;
    }
    
    private function hasFilters(array $requestData): bool
    {
        return !empty($requestData['filters']) || !empty($requestData['where']);
    }
}
