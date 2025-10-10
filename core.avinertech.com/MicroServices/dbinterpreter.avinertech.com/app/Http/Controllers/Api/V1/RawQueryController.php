<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RawQueryRequest;
use App\Services\RawQueryService;
use Illuminate\Http\Request;

class RawQueryController extends Controller
{
    public function __construct(
        private RawQueryService $rawQueryService
    ) {}

    public function execute(RawQueryRequest $request)
    {
        $tenantId = $request->get('tenant_id');
        $query = $request->input('query');
        
        try {
            $result = $this->rawQueryService->executeSafeQuery($query, $tenantId);
            
            return response()->json([
                'status' => 'success',
                'message' => __('messages.query_executed_successfully'),
                'data' => $result
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.query_execution_failed'),
                'error' => [
                    'code' => 'QUERY_EXECUTION_FAILED',
                    'details' => $e->getMessage()
                ]
            ], 400);
        }
    }
}
