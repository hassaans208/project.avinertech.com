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

                    <!-- Modules -->
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Package Modules</h4>
                        @if($package->modules && count($package->modules) > 0)
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($package->modules as $module)
                                    <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                        <span class="text-sm font-medium text-blue-900">
                                            {{ str_replace('_', ' ', ucwords($module)) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 italic">No modules assigned to this package.</p>
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

            <!-- Available Modules Reference -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Modules</h3>
                    
                    <div class="space-y-2">
                        @foreach(\App\Models\Package::getAvailableModules() as $module)
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-700">
                                    {{ str_replace('_', ' ', ucwords($module)) }}
                                </span>
                                @if(in_array($module, $package->modules ?? []))
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center text-xs text-gray-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            <span class="mr-4">Included</span>
                            <span class="w-2 h-2 bg-gray-300 rounded-full mr-2"></span>
                            <span>Not included</span>
                        </div>
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