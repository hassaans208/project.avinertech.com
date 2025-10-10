<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Package Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('packages.edit', ['id' => $package->id, 'access_token' => $accessToken]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Edit Package
                </a>
                <a href="{{ route('packages.index', ['access_token' => $accessToken]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Packages
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Package Information -->
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">{{ $package->name }}</h3>
                            <p class="text-3xl font-bold text-indigo-600 mt-2">${{ $package->cost }} {{ $package->currency }}</p>
                            @if($package->tax_rate > 0)
                                <p class="text-sm text-gray-500 mt-1">Tax Rate: {{ ($package->tax_rate * 100) }}%</p>
                            @endif
                        </div>
                        @if($package->isFree())
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                                FREE PACKAGE
                            </span>
                        @endif
                    </div>
                    
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Package Name</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $package->name }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cost</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ $package->cost }} {{ $package->currency }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tax Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ($package->tax_rate * 100) }}%</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Active Subscriptions</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $package->tenants->count() }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $package->created_at->format('M d, Y \a\t g:i A') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $package->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                        </div>
                    </dl>
                    
                    <!-- Service Modules -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Service Modules</h4>
                        @if($package->serviceModules && $package->serviceModules->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($package->serviceModules as $module)
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-medium text-gray-900">{{ $module->display_name }}</h5>
                                                @if($module->description)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-medium text-gray-900">${{ number_format($module->sale_price, 2) }}</div>
                                                @if($module->tax_rate > 0)
                                                    <div class="text-xs text-gray-500">+${{ number_format($module->tax_amount, 2) }} tax</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-between text-xs text-gray-500">
                                            <span>Cost: ${{ number_format($module->cost_price, 2) }}</span>
                                            <span>Profit: ${{ number_format($module->profit, 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Package Totals -->
                            <div class="mt-4 bg-indigo-50 rounded-lg p-4">
                                <h5 class="font-medium text-indigo-900 mb-2">Package Totals</h5>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-indigo-600">Total Cost:</span>
                                        <div class="font-medium">${{ number_format($package->total_cost_price, 2) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-indigo-600">Total Sale:</span>
                                        <div class="font-medium">${{ number_format($package->total_sale_price, 2) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-indigo-600">Total Tax:</span>
                                        <div class="font-medium">${{ number_format($package->total_tax, 2) }}</div>
                                    </div>
                                    <div>
                                        <span class="text-indigo-600">Total with Tax:</span>
                                        <div class="font-medium">${{ number_format($package->total_sale_price_incl_tax, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 italic">No service modules assigned to this package.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tenants Using This Package -->
            <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Tenants Using This Package ({{ $package->tenants->count() }})
                    </h3>
                    
                    @if($package->tenants->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($package->tenants->sortByDesc('pivot.registered_at') as $tenant)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $tenant->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                                {{ $tenant->host }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    @if($tenant->status === 'active') bg-green-100 text-green-800
                                                    @elseif($tenant->status === 'blocked') bg-red-100 text-red-800
                                                    @else bg-yellow-100 text-yellow-800 @endif">
                                                    {{ ucfirst($tenant->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($tenant->pivot->registered_at)->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('tenants.show', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    View Tenant
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No tenants are currently using this package.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Package Stats & Actions -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Package Statistics</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Subscriptions:</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $package->tenants->count() }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Tenants:</span>
                            <span class="text-lg font-semibold text-green-600">
                                {{ $package->tenants->where('status', 'active')->count() }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Blocked Tenants:</span>
                            <span class="text-lg font-semibold text-red-600">
                                {{ $package->tenants->where('status', 'blocked')->count() }}
                            </span>
                        </div>
                        
                        @if($package->cost > 0)
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Monthly Revenue:</span>
                                    <span class="text-lg font-semibold text-indigo-600">
                                        ${{ number_format($package->cost * $package->tenants->where('status', 'active')->count(), 2) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Package Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('packages.edit', ['id' => $package->id, 'access_token' => $accessToken]) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center block">
                            Edit Package
                        </a>
                        
                        @if($package->tenants->count() === 0)
                            <button class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                    onclick="alert('Package deletion is not implemented for safety. Contact administrator.')">
                                Delete Package
                            </button>
                        @else
                            <div class="text-xs text-gray-500 text-center">
                                Cannot delete package with active subscriptions
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 