<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RawQueryService
{
    private const PROHIBITED_PATTERNS = [
        '/\bDROP\b/i',
        '/\bCREATE\b/i',
        '/\bALTER\b/i',
        '/\bINSERT\b/i',
        '/\bUPDATE\b/i',
        '/\bDELETE\b/i',
        '/\bTRUNCATE\b/i',
        '/\bGRANT\b/i',
        '/\bREVOKE\b/i',
        '/\bKILL\b/i',
        '/\bSHUTDOWN\b/i',
        '/\bFLUSH\b/i',
        '/\bRESET\b/i',
        '/\b1\s*=\s*1\b/i', // Prevent 1=1 comparisons
        '/\b\d+\s*=\s*\d+\b/i', // Prevent numeric comparisons
        '/\bUNION\b/i',
        '/\bEXEC\b/i',
        '/\bEXECUTE\b/i',
        '/\bSP_\w+/i', // Prevent stored procedure calls
        '/\bxp_\w+/i', // Prevent extended procedure calls
    ];

    public function executeSafeQuery(string $query, string $tenantId): array
    {
        // 1. Validate query is SELECT only
        $this->validateSelectOnly($query);
        
        // 2. Check for prohibited patterns
        $this->validateProhibitedPatterns($query);
        
        // 3. Validate column-value comparisons only
        $this->validateColumnValueComparisons($query);
        
        // 4. Apply query limits
        $limitedQuery = $this->applyQueryLimits($query);
        
        // 5. Execute query
        $results = DB::connection('ui_api')->select($limitedQuery);
        
        // 6. Log query execution
        Log::info('Raw query executed', [
            'tenant_id' => $tenantId,
            'query' => $limitedQuery,
            'result_count' => count($results)
        ]);
        
        return [
            'results' => $results,
            'count' => count($results),
            'query_executed' => $limitedQuery,
            'limited' => $this->wasQueryLimited($query, $limitedQuery)
        ];
    }

    private function validateSelectOnly(string $query): void
    {
        $queryUpper = strtoupper(trim($query));
        
        if (!str_starts_with($queryUpper, 'SELECT')) {
            throw new \InvalidArgumentException('Only SELECT queries are allowed');
        }
        
        // Check for multiple statements
        if (strpos($queryUpper, ';') !== false && substr_count($queryUpper, ';') > 1) {
            throw new \InvalidArgumentException('Multiple statements not allowed');
        }
    }

    private function validateProhibitedPatterns(string $query): void
    {
        foreach (self::PROHIBITED_PATTERNS as $pattern) {
            if (preg_match($pattern, $query)) {
                throw new \InvalidArgumentException('Query contains prohibited patterns');
            }
        }
    }

    private function validateColumnValueComparisons(string $query): void
    {
        // Extract WHERE clauses
        preg_match_all('/WHERE\s+(.+?)(?:\s+GROUP\s+BY|\s+ORDER\s+BY|\s+HAVING|\s+LIMIT|$)/i', $query, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $whereClause) {
                // Check for value-to-value comparisons
                if (preg_match('/\b\d+\s*[=<>!]+\s*\d+\b/', $whereClause)) {
                    throw new \InvalidArgumentException('Value-to-value comparisons not allowed');
                }
                
                // Check for suspicious patterns
                if (preg_match('/\b\w+\s*=\s*\w+\s*AND\s*\w+\s*=\s*\w+\b/i', $whereClause)) {
                    throw new \InvalidArgumentException('Suspicious comparison pattern detected');
                }
            }
        }
    }

    private function applyQueryLimits(string $query): string
    {
        $queryUpper = strtoupper($query);
        
        // Check if LIMIT already exists
        if (strpos($queryUpper, 'LIMIT') !== false) {
            // Extract existing limit
            preg_match('/LIMIT\s+(\d+)/i', $query, $matches);
            $existingLimit = isset($matches[1]) ? (int)$matches[1] : 1000;
            
            // Apply maximum limit
            $maxLimit = 1000; // Default limit for raw queries
            $newLimit = min($existingLimit, $maxLimit);
            
            return preg_replace('/LIMIT\s+\d+/i', "LIMIT {$newLimit}", $query);
        }
        
        // Add LIMIT if not present
        return $query . " LIMIT 1000";
    }

    private function wasQueryLimited(string $originalQuery, string $limitedQuery): bool
    {
        return $originalQuery !== $limitedQuery;
    }
}
