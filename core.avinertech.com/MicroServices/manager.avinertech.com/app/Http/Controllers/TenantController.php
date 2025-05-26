<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\SshService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    protected $sshService;

    public function __construct(SshService $sshService)
    {
        $this->sshService = $sshService;
    }

    /**
     * Display a listing of the tenants.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Tenant::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tenant_url', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply status filters
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            } elseif ($request->status === 'paid') {
                $query->where('is_paid', true);
            } elseif ($request->status === 'unpaid') {
                $query->where('is_paid', false);
            }
        }

        $tenants = $query->latest()->paginate(10);

        return view('tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created tenant in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tenant_url' => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:tenants,tenant_url',
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'database_name' => 'required|string|max:64|regex:/^[a-z0-9_]+$/',
            'database_user' => 'required|string|max:32|regex:/^[a-z0-9_]+$/',
            'database_password' => 'required|string|min:8',
            'module' => 'required|in:core,tenant',
            'submodule' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Create tenant record
            $tenant = Tenant::create([
                'tenant_url' => $validatedData['tenant_url'],
                'company_name' => $validatedData['company_name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'address' => $validatedData['address'] ?? null,
                'database_name' => $validatedData['database_name'],
                'database_user' => $validatedData['database_user'],
                'database_password' => bcrypt($validatedData['database_password']),
                'is_active' => true,
                'is_paid' => false,
                'is_blocked' => false,
                'module' => $validatedData['module'],
                'submodule' => $validatedData['submodule'],
            ]);

            DB::commit();

            // Check if we need to perform additional actions
            if ($request->filled('action')) {
                $action = $request->action;
                if ($action === 'create_module') {
                    return redirect()->route('tenants.create-module', $tenant);
                } elseif ($action === 'deploy_module') {
                    return redirect()->route('tenants.deploy-module', $tenant);
                } elseif ($action === 'ssl_cert') {
                    return redirect()->route('tenants.ssl-cert', $tenant);
                } elseif ($action === 'create_database') {
                    return redirect()->route('tenants.create-database', $tenant);
                }
            }

            return redirect()->route('tenants.index')
                ->with('success', 'Tenant created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed to create tenant: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\View\View
     */
    public function edit(Tenant $tenant)
    {
        return view('tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant in storage.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
            'is_paid' => 'nullable|boolean',
            'is_blocked' => 'nullable|boolean',
        ]);

        try {
            // Convert checkbox values
            $validatedData['is_active'] = $request->has('is_active');
            $validatedData['is_paid'] = $request->has('is_paid');
            $validatedData['is_blocked'] = $request->has('is_blocked');

            $tenant->update($validatedData);
            
            return redirect()->route('tenants.index')
                ->with('success', 'Tenant updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update tenant: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to update tenant: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified tenant from storage.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tenant $tenant)
    {
        try {
            $tenant->delete();
            return redirect()->route('tenants.index')
                ->with('success', 'Tenant deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete tenant: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete tenant: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the payment status of the specified tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function togglePayment(Tenant $tenant)
    {
        try {
            $tenant->is_paid = !$tenant->is_paid;
            $tenant->save();
            
            return back()->with('success', 'Payment status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update payment status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update payment status.');
        }
    }

    /**
     * Toggle the block status of the specified tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleBlock(Tenant $tenant)
    {
        try {
            $tenant->is_blocked = !$tenant->is_blocked;
            $tenant->save();
            
            return back()->with('success', 'Block status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update block status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update block status.');
        }
    }

    /**
     * Get tenant details by host domain.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTenantByHost()
    {
        try {
            $host = request()->getHost();
            
            $tenant = Tenant::where('host', $host)->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $tenant
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tenant by host: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tenant details'
            ], 500);
        }
    }

    public function updateTenantByHost(Request $request, $host)
    {
        try {
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string|max:500',
                'username' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);

            $tenant = Tenant::where('host', $host)->firstOrFail();

            $tenant->update($validatedData);

            return response()->json([
                'status' => 'success',
                'data' => $tenant
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tenant by host: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch tenant details'
            ], 500);
        }
    }
} 