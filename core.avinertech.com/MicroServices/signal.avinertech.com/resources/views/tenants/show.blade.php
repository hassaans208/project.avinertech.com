<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tenant Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('tenants.edit', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Tenant
                </a>
                <a href="{{ route('tenants.index', ['access_token' => $accessToken]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Tenants
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tenant Information -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tenant Information</h3>
                    
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tenant->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Host</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $tenant->host }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($tenant->status === 'active') bg-green-100 text-green-800
                                    @elseif($tenant->status === 'blocked') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tenant->created_at->format('M d, Y \a\t g:i A') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tenant->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                        </div>
                        
                        @if($tenant->status === 'blocked' && $tenant->block_reason)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Block Reason</dt>
                                <dd class="mt-1 text-sm text-red-600 bg-red-50 p-3 rounded">{{ $tenant->block_reason }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Package History -->
            <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Package History</h3>
                    
                    @if($tenant->packages->count() > 0)
                        <div class="space-y-3">
                            @foreach($tenant->packages->sortByDesc('pivot.registered_at') as $package)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $package->name }}</h4>
                                            <p class="text-sm text-gray-500">${{ $package->cost }} {{ $package->currency }}</p>
                                            @if($package->modules)
                                                <div class="mt-2 flex flex-wrap gap-1">
                                                    @foreach($package->modules as $module)
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $module }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Registered</p>
                                            <p class="text-sm font-medium">{{ \Carbon\Carbon::parse($package->pivot->registered_at)->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No packages assigned to this tenant.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions & Quick Info -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        @if($tenant->status === 'blocked')
                            <form action="{{ route('tenants.unblock', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to unblock this tenant?')">
                                    Unblock Tenant
                                </button>
                            </form>
                        @else
                            <button onclick="showBlockModal()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Block Tenant
                            </button>
                        @endif
                        
                        <form action="{{ route('tenants.change-status', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" method="POST">
                            @csrf
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Change Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="this.form.submit()">
                                    <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $tenant->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="blocked" {{ $tenant->status === 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Current Package Info -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Package</h3>
                    
                    @php $currentPackage = $tenant->getCurrentPackage(); @endphp
                    @if($currentPackage)
                        <div class="space-y-2">
                            <p class="font-medium">{{ $currentPackage->name }}</p>
                            <p class="text-sm text-gray-600">${{ $currentPackage->cost }} {{ $currentPackage->currency }}</p>
                            <p class="text-xs text-gray-500">Tax Rate: {{ ($currentPackage->tax_rate * 100) }}%</p>
                            
                            @if($currentPackage->modules)
                                <div class="mt-3">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Modules:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($currentPackage->modules as $module)
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">{{ $module }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500">No package assigned</p>
                    @endif
                </div>
            </div>

            <!-- Signal Logs Summary -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Signal Activity</h3>
                    
                    @php 
                        $recentLogs = $tenant->signalLogs()->recent(24)->get();
                        $successCount = $recentLogs->where('status', 'success')->count();
                        $failedCount = $recentLogs->where('status', '!=', 'success')->count();
                    @endphp
                    
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Last 24 hours:</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-green-600">Successful:</span>
                            <span class="font-medium">{{ $successCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-red-600">Failed:</span>
                            <span class="font-medium">{{ $failedCount }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total:</span>
                            <span class="font-medium">{{ $recentLogs->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Block Modal -->
    <div id="blockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Block Tenant</h3>
                <form action="{{ route('tenants.block', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="block_reason" class="block text-sm font-medium text-gray-700 mb-2">Block Reason</label>
                        <textarea name="block_reason" id="block_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Enter reason for blocking this tenant..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideBlockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Block Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showBlockModal() {
            document.getElementById('blockModal').classList.remove('hidden');
        }

        function hideBlockModal() {
            document.getElementById('blockModal').classList.add('hidden');
            document.getElementById('block_reason').value = '';
        }
    </script>
</x-app-layout> 