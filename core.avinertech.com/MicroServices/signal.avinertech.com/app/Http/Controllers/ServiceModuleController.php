<?php

namespace App\Http\Controllers;

use App\Models\ServiceModule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ServiceModuleController extends Controller
{
    /**
     * Get access token from request
     */
    private function getAccessToken(Request $request): string
    {
        return $request->header('Authorization', $request->query('access_token', ''));
    }

    /**
     * Get all service modules (API endpoint)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = ServiceModule::query();

            // Filter by active status if requested
            if ($request->has('active_only') && $request->boolean('active_only')) {
                $query->active();
            }

            $modules = $query->orderBy('name')->get();
            
            $modulesData = $modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'name' => $module->name,
                    'display_name' => $module->display_name,
                    'description' => $module->description,
                    'cost_price' => number_format($module->cost_price, 2),
                    'sale_price' => number_format($module->sale_price, 2),
                    'tax_rate' => number_format($module->tax_rate, 4),
                    'tax_amount' => number_format($module->tax_amount, 2),
                    'sale_price_incl_tax' => number_format($module->sale_price_incl_tax, 2),
                    'profit' => number_format($module->profit, 2),
                    'currency' => $module->currency,
                    'is_active' => $module->is_active,
                    'formatted_cost_price' => $module->formatted_cost_price,
                    'formatted_sale_price' => $module->formatted_sale_price,
                    'formatted_sale_price_incl_tax' => $module->formatted_sale_price_incl_tax,
                    'created_at' => $module->created_at->toISOString(),
                    'updated_at' => $module->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $modulesData,
                'total_modules' => $modules->count(),
                'message' => 'Service modules retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve service modules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of service modules (Web)
     */
    public function webIndex(Request $request): View
    {
        $modules = ServiceModule::orderBy('name')->get();
        $accessToken = $this->getAccessToken($request);
        
        return view('service-modules.index', compact('modules', 'accessToken'));
    }

    /**
     * Show the form for creating a new service module (Web)
     */
    public function webCreate(Request $request): View
    {
        $accessToken = $this->getAccessToken($request);
        
        return view('service-modules.create', compact('accessToken'));
    }

    /**
     * Store a newly created service module (Web)
     */
    public function webStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_modules,name|regex:/^[a-z][a-z0-9_]*$/',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0|max:999999.99',
            'sale_price' => 'required|numeric|min:0|max:999999.99',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
            'is_active' => 'boolean',
        ]);

        ServiceModule::create($validated);

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('service-modules.index', ['access_token' => $accessToken])
            ->with('success', 'Service module created successfully.');
    }

    /**
     * Display the specified service module (Web)
     */
    public function webShow(Request $request, int $id): View
    {
        $module = ServiceModule::findOrFail($id);
        $accessToken = $this->getAccessToken($request);
        
        return view('service-modules.show', compact('module', 'accessToken'));
    }

    /**
     * Show the form for editing the specified service module (Web)
     */
    public function webEdit(Request $request, int $id): View
    {
        $module = ServiceModule::findOrFail($id);
        $accessToken = $this->getAccessToken($request);
        
        return view('service-modules.edit', compact('module', 'accessToken'));
    }

    /**
     * Update the specified service module (Web)
     */
    public function webUpdate(Request $request, int $id): RedirectResponse
    {
        $module = ServiceModule::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::unique('service_modules', 'name')->ignore($id),
            ],
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0|max:999999.99',
            'sale_price' => 'required|numeric|min:0|max:999999.99',
            'tax_rate' => 'required|numeric|min:0|max:1',
            'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
            'is_active' => 'boolean',
        ]);

        $module->update($validated);

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('service-modules.index', ['access_token' => $accessToken])
            ->with('success', 'Service module updated successfully.');
    }

    /**
     * Remove the specified service module (Web)
     */
    public function webDestroy(Request $request, int $id): RedirectResponse
    {
        $module = ServiceModule::findOrFail($id);
        
        // Check if module is associated with any packages
        if ($module->packages()->count() > 0) {
            $accessToken = $this->getAccessToken($request);
            return redirect()->route('service-modules.index', ['access_token' => $accessToken])
                ->with('error', 'Cannot delete service module that is associated with packages.');
        }

        $module->delete();

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('service-modules.index', ['access_token' => $accessToken])
            ->with('success', 'Service module deleted successfully.');
    }

    /**
     * Store a newly created service module
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:service_modules,name|regex:/^[a-z][a-z0-9_]*$/',
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0|max:999999.99',
                'sale_price' => 'required|numeric|min:0|max:999999.99',
                'tax_rate' => 'required|numeric|min:0|max:1',
                'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
                'is_active' => 'boolean',
            ]);

            $module = ServiceModule::create($validated);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'display_name' => $module->display_name,
                    'description' => $module->description,
                    'cost_price' => number_format($module->cost_price, 2),
                    'sale_price' => number_format($module->sale_price, 2),
                    'tax_rate' => number_format($module->tax_rate, 4),
                    'currency' => $module->currency,
                    'is_active' => $module->is_active,
                ],
                'message' => 'Service module created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create service module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service module
     */
    public function show(int $id): JsonResponse
    {
        try {
            $module = ServiceModule::find($id);

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'error' => 'Service module not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'display_name' => $module->display_name,
                    'description' => $module->description,
                    'cost_price' => number_format($module->cost_price, 2),
                    'sale_price' => number_format($module->sale_price, 2),
                    'tax_rate' => number_format($module->tax_rate, 4),
                    'tax_amount' => number_format($module->tax_amount, 2),
                    'sale_price_incl_tax' => number_format($module->sale_price_incl_tax, 2),
                    'profit' => number_format($module->profit, 2),
                    'currency' => $module->currency,
                    'is_active' => $module->is_active,
                    'formatted_cost_price' => $module->formatted_cost_price,
                    'formatted_sale_price' => $module->formatted_sale_price,
                    'formatted_sale_price_incl_tax' => $module->formatted_sale_price_incl_tax,
                    'created_at' => $module->created_at->toISOString(),
                    'updated_at' => $module->updated_at->toISOString(),
                ],
                'message' => 'Service module retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve service module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified service module
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $module = ServiceModule::find($id);

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'error' => 'Service module not found'
                ], 404);
            }

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-z][a-z0-9_]*$/',
                    Rule::unique('service_modules', 'name')->ignore($id),
                ],
                'display_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'cost_price' => 'required|numeric|min:0|max:999999.99',
                'sale_price' => 'required|numeric|min:0|max:999999.99',
                'tax_rate' => 'required|numeric|min:0|max:1',
                'currency' => 'required|string|size:3|regex:/^[A-Z]{3}$/',
                'is_active' => 'boolean',
            ]);

            $module->update($validated);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $module->id,
                    'name' => $module->name,
                    'display_name' => $module->display_name,
                    'description' => $module->description,
                    'cost_price' => number_format($module->cost_price, 2),
                    'sale_price' => number_format($module->sale_price, 2),
                    'tax_rate' => number_format($module->tax_rate, 4),
                    'currency' => $module->currency,
                    'is_active' => $module->is_active,
                ],
                'message' => 'Service module updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update service module: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified service module
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $module = ServiceModule::find($id);

            if (!$module) {
                return response()->json([
                    'success' => false,
                    'error' => 'Service module not found'
                ], 404);
            }

            $module->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service module deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete service module: ' . $e->getMessage()
            ], 500);
        }
    }
} 