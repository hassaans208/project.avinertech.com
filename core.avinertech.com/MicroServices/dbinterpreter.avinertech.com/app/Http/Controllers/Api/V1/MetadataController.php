<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FilterResource;
use App\Http\Resources\AggregationResource;
use App\Http\Resources\ColumnResource;
use App\Services\MetadataService;
use Illuminate\Http\Request;

class MetadataController extends Controller
{
    public function __construct(
        private MetadataService $metadataService
    ) {}

    public function getFilters(Request $request)
    {
        $filters = $this->metadataService->getAllFilterOperators();
        
        return response()->json([
            'status' => 'success',
            'message' => __('messages.filters_retrieved_successfully'),
            'data' => FilterResource::collection($filters)
        ], 200);
    }

    public function getAggregations(Request $request)
    {
        $aggregations = $this->metadataService->getAllAggregationFunctions();
        
        return response()->json([
            'status' => 'success',
            'message' => __('messages.aggregations_retrieved_successfully'),
            'data' => AggregationResource::collection($aggregations)
        ], 200);
    }

    public function getAllColumns(Request $request)
    {
        $tenantId = $request->get('tenant_id');
        $schemaName = $request->get('schema_name');
        $columns = $this->metadataService->getAllTenantColumns($tenantId, $schemaName);
        
        return response()->json([
            'status' => 'success',
            'message' => __('messages.columns_retrieved_successfully'),
            'data' => ColumnResource::collection($columns)
        ], 200);
    }
}
