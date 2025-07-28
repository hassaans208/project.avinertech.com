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
            $packages = $this->packageRepository->getAll();
            
            $packagesData = $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'cost' => number_format($package->cost, 2),
                    'currency' => $package->currency,
                    'tax_rate' => number_format($package->tax_rate, 4),
                    'modules' => $package->modules ?? [],
                    'is_free' => $package->isFree(),
                    'formatted_cost' => $package->getFormattedCostAttribute(),
                    'available_modules' => Package::getAvailableModules(),
                    'created_at' => $package->created_at->toISOString(),
                    'updated_at' => $package->updated_at->toISOString(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $packagesData,
                'total_packages' => $packages->count(),
                'available_modules' => Package::getAvailableModules(),
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
        $packages = $this->packageRepository->getAll();
        $accessToken = $this->getAccessToken($request);
        
        return view('packages.index', compact('packages', 'accessToken'));
    }

    /**
     * Show the form for creating a new package.
     */
    public function create(Request $request): View
    {
        $availableModules = Package::getAvailableModules();
        $accessToken = $this->getAccessToken($request);
        
        return view('packages.create', compact('availableModules', 'accessToken'));
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
        $package = $this->packageRepository->findById($id);
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
        $package = $this->packageRepository->findById($id);
        $availableModules = Package::getAvailableModules();
        $accessToken = $this->getAccessToken($request);
        
        if (!$package) {
            abort(404);
        }

        return view('packages.edit', compact('package', 'availableModules', 'accessToken'));
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
} 