<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class TenantSiteController extends Controller
{
    /**
     * Get tenant details by tenant_id
     * 
     * @param Request $request
     * @param string $tenant_id
     * @return JsonResponse
     */
    public function getTenantInfo(Request $request, $tenant_id): JsonResponse
    {
        try {
            // Find the tenant by tenant_id (not the primary key)
            $tenant = Tenant::where('tenant_id', $tenant_id)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                ], 404);
            }

            // Load the tenant information
            return response()->json([
                'success' => true,
                'data' => $tenant,
                'message' => 'Tenant information retrieved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving tenant information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download tenant information in specified format
     * 
     * @param Request $request
     * @param string $tenant_id
     * @param string $format (json, csv, xml)
     * @return mixed
     */
    public function downloadTenantInfo(Request $request, $tenant_id, $format = 'json')
    {
        try {
            // Find the tenant by tenant_id
            $tenant = Tenant::where('tenant_id', $tenant_id)->first();

            if (!$tenant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant not found',
                ], 404);
            }

            // Prepare tenant data
            $tenantData = $tenant->toArray();
            
            // Create filename
            $filename = "tenant_{$tenant_id}_" . date('Y-m-d') . ".{$format}";
            
            // Return data in requested format
            switch ($format) {
                case 'csv':
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    // Create CSV
                    $callback = function() use ($tenantData) {
                        $file = fopen('php://output', 'w');
                        
                        // Add headers
                        fputcsv($file, array_keys($tenantData));
                        
                        // Add data
                        fputcsv($file, array_values($tenantData));
                        
                        fclose($file);
                    };
                    
                    return Response::stream($callback, 200, $headers);
                    
                case 'xml':
                    $xml = new \SimpleXMLElement('<tenant></tenant>');
                    
                    // Convert array to XML
                    foreach ($tenantData as $key => $value) {
                        if ($value !== null) {
                            $xml->addChild($key, (string)$value);
                        } else {
                            $xml->addChild($key, '');
                        }
                    }
                    
                    $headers = [
                        'Content-Type' => 'application/xml',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    return Response::make($xml->asXML(), 200, $headers);
                    
                case 'json':
                default:
                    $headers = [
                        'Content-Type' => 'application/json',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ];
                    
                    return Response::make(json_encode($tenantData, JSON_PRETTY_PRINT), 200, $headers);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading tenant information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
} 