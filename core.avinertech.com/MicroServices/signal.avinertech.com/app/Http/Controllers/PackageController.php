<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Http\Requests\PackageRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller
{
    public function __construct(
        private PackageRepositoryInterface $packageRepository
    ) {}

    /**
     * Get access token from request
     */
    private function getAccessToken(Request $request): string
    {
        return $request->header('Authorization', $request->query('access_token', ''));
    }

    /**
     * Get all packages with their modules (API endpoint)
     */
    public function getPackages(Request $request): JsonResponse
    {
        try {
            $packages = $this->packageRepository->getAllWithServiceModules();
            
            $packagesData = $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'cost' => number_format($package->cost, 2),
                    'currency' => $package->currency,
                    'tax_rate' => number_format($package->tax_rate, 4),
                    'service_modules' => $package->serviceModules->map(function ($module) {
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
                        ];
                    }),
                    'totals' => [
                        'total_cost_price' => number_format($package->total_cost_price, 2),
                        'total_sale_price' => number_format($package->total_sale_price, 2),
                        'total_tax' => number_format($package->total_tax, 2),
                        'total_sale_price_incl_tax' => number_format($package->total_sale_price_incl_tax, 2),
                        'total_profit' => number_format($package->total_profit, 2),
                        'formatted_total_cost_price' => $package->formatted_total_cost_price,
                        'formatted_total_sale_price' => $package->formatted_total_sale_price,
                        'formatted_total_sale_price_incl_tax' => $package->formatted_total_sale_price_incl_tax,
                    ],
                    'is_free' => $package->isFree(),
                    'formatted_cost' => $package->getFormattedCostAttribute(),
                    'created_at' => $package->created_at->toISOString(),
                    'updated_at' => $package->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $packagesData,
                'total_packages' => $packages->count(),
                'message' => 'Packages retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve packages: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of packages.
     */
    public function index(Request $request): View
    {
        $packages = $this->packageRepository->getAllWithServiceModules();
        $accessToken = $this->getAccessToken($request);
        
        return view('packages.index', compact('packages', 'accessToken'));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create(Request $request): View
    {
        $accessToken = $this->getAccessToken($request);
        
        return view('packages.create', compact('accessToken'));
    }

    /**
     * Store a newly created package.
     */
    public function store(PackageRequest $request): RedirectResponse
    {
        $this->packageRepository->create($request->validated());

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('packages.index', ['access_token' => $accessToken])
            ->with('success', 'Package created successfully.');
    }

    /**
     * Display the specified package.
     */
    public function show(Request $request, int $id): View
    {
        $package = $this->packageRepository->findByIdWithServiceModules($id);
        $accessToken = $this->getAccessToken($request);
        
        if (!$package) {
            abort(404);
        }

        return view('packages.show', compact('package', 'accessToken'));
    }

    /**
     * Show the form for editing the specified package.
     */
    public function edit(Request $request, int $id): View
    {
        $package = $this->packageRepository->findByIdWithServiceModules($id);
        $accessToken = $this->getAccessToken($request);
        
        if (!$package) {
            abort(404);
        }

        return view('packages.edit', compact('package', 'accessToken'));
    }

    /**
     * Update the specified package.
     */
    public function update(PackageRequest $request, int $id): RedirectResponse
    {
        $package = $this->packageRepository->findById($id);
        
        if (!$package) {
            abort(404);
        }

        $this->packageRepository->update($package, $request->validated());

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('packages.index', ['access_token' => $accessToken])
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Attach a service module to a package
     */
    public function attachModule(Request $request, int $id): RedirectResponse
    {
        $package = $this->packageRepository->findByIdWithServiceModules($id);
        
        if (!$package) {
            abort(404);
        }

        $request->validate([
            'service_module_id' => 'required|exists:service_modules,id'
        ]);

        $moduleId = $request->input('service_module_id');
        
        // Check if module is already attached
        if (!$package->serviceModules()->where('service_module_id', $moduleId)->exists()) {
            $package->serviceModules()->attach($moduleId);
        }

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('packages.edit', ['id' => $id, 'access_token' => $accessToken])
            ->with('success', 'Service module attached successfully.');
    }

    /**
     * Detach a service module from a package
     */
    public function detachModule(Request $request, int $id, int $moduleId): RedirectResponse
    {
        $package = $this->packageRepository->findByIdWithServiceModules($id);
        
        if (!$package) {
            abort(404);
        }

        $package->serviceModules()->detach($moduleId);

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('packages.edit', ['id' => $id, 'access_token' => $accessToken])
            ->with('success', 'Service module detached successfully.');
    }
} 