<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Http\Requests\TenantRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TenantController extends Controller
{
    public function __construct(
        private TenantRepositoryInterface $tenantRepository,
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
     * Display a listing of tenants.
     */
    public function index(Request $request): View
    {
        $tenants = $this->tenantRepository->getAllPaginated(15);
        $accessToken = $this->getAccessToken($request);
        
        return view('tenants.index', compact('tenants', 'accessToken'));
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create(Request $request): View
    {
        $packages = $this->packageRepository->getAll();
        $accessToken = $this->getAccessToken($request);
        
        return view('tenants.create', compact('packages', 'accessToken'));
    }

    /**
     * Store a newly created tenant.
     */
    public function store(TenantRequest $request): RedirectResponse
    {
        $tenant = $this->tenantRepository->create($request->validated());

        // Assign selected package if provided
        if ($request->has('package_id')) {
            $package = $this->packageRepository->findById($request->package_id);
            if ($package) {
                $this->tenantRepository->assignPackage($tenant, $package);
            }
        }

        $accessToken = $this->getAccessToken($request);
        
        return redirect()->route('tenants.index', ['access_token' => $accessToken])
            ->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified tenant.
     */
    public function show(Request $request, int $id): View
    {
        $tenant = $this->tenantRepository->findById($id);
        $accessToken = $this->getAccessToken($request);
        
        if (!$tenant) {
            abort(404);
        }

        return view('tenants.show', compact('tenant', 'accessToken'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Request $request, int $id): View
    {
        $tenant = $this->tenantRepository->findById($id);
        $packages = $this->packageRepository->getAll();
        $accessToken = $this->getAccessToken($request);
        
        if (!$tenant) {
            abort(404);
        }

        return view('tenants.edit', compact('tenant', 'packages', 'accessToken'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(TenantRequest $request, int $id): RedirectResponse
    {
        $tenant = $this->tenantRepository->findById($id);
        
        if (!$tenant) {
            abort(404);
        }

        $tenant->update($request->validated());

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('tenants.index', ['access_token' => $accessToken])
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Block a tenant.
     */
    public function block(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'block_reason' => 'required|string|max:500'
        ]);

        $tenant = $this->tenantRepository->findById($id);
        
        if (!$tenant) {
            abort(404);
        }

        $this->tenantRepository->updateStatus($tenant, 'blocked', $request->block_reason);

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('tenants.index', ['access_token' => $accessToken])
            ->with('success', 'Tenant blocked successfully.');
    }

    /**
     * Unblock a tenant.
     */
    public function unblock(Request $request, int $id): RedirectResponse
    {
        $tenant = $this->tenantRepository->findById($id);
        
        if (!$tenant) {
            abort(404);
        }

        $this->tenantRepository->updateStatus($tenant, 'active');

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('tenants.index', ['access_token' => $accessToken])
            ->with('success', 'Tenant unblocked successfully.');
    }

    /**
     * Change tenant status.
     */
    public function changeStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:active,inactive,blocked',
            'block_reason' => 'nullable|string|max:500|required_if:status,blocked'
        ]);

        $tenant = $this->tenantRepository->findById($id);
        
        if (!$tenant) {
            abort(404);
        }

        $this->tenantRepository->updateStatus(
            $tenant, 
            $request->status, 
            $request->block_reason
        );

        $accessToken = $this->getAccessToken($request);

        return redirect()->route('tenants.index', ['access_token' => $accessToken])
            ->with('success', 'Tenant status updated successfully.');
    }
} 