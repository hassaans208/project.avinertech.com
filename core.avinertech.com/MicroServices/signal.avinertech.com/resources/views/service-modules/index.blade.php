<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Service Modules') }}
            </h2>
            <a href="{{ route('service-modules.create', ['access_token' => $accessToken]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Module
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($modules as $module)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $module->display_name }}</h3>
                                <p class="text-sm text-gray-500 font-mono">{{ $module->name }}</p>
                                @if($module->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ Str::limit($module->description, 80) }}</p>
                                @endif
                            </div>
                            @if(!$module->is_active)
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">
                                    INACTIVE
                                </span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    ACTIVE
                                </span>
                            @endif
                        </div>

                        <!-- Pricing Information -->
                        <div class="mb-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cost Price:</span>
                                <span class="font-medium">${{ number_format($module->cost_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Sale Price:</span>
                                <span class="font-medium text-green-600">${{ number_format($module->sale_price, 2) }}</span>
                            </div>
                            @if($module->tax_rate > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax ({{ number_format($module->tax_rate * 100, 2) }}%):</span>
                                    <span class="font-medium">${{ number_format($module->tax_amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm border-t pt-2">
                                    <span class="text-gray-600">Total with Tax:</span>
                                    <span class="font-bold">${{ number_format($module->sale_price_incl_tax, 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Profit:</span>
                                <span class="font-medium {{ $module->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    ${{ number_format($module->profit, 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Package Usage -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                Used in <span class="font-medium">{{ $module->packages->count() }}</span> 
                                {{ Str::plural('package', $module->packages->count()) }}
                            </p>
                            @if($module->packages->count() > 0)
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($module->packages->take(3) as $package)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                            {{ $package->name }}
                                        </span>
                                    @endforeach
                                    @if($module->packages->count() > 3)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                            +{{ $module->packages->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="space-x-2">
                                <a href="{{ route('service-modules.show', ['id' => $module->id, 'access_token' => $accessToken]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('service-modules.edit', ['id' => $module->id, 'access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Edit
                                </a>
                                @if($module->packages->count() === 0)
                                    <form action="{{ route('service-modules.destroy', ['id' => $module->id, 'access_token' => $accessToken]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium" onclick="return confirm('Are you sure you want to delete this service module?')">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">
                                {{ $module->currency }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <p class="text-lg mb-2">No service modules found.</p>
                            <a href="{{ route('service-modules.create', ['access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900">Create your first service module</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Service Module Summary Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ $modules->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Modules</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $modules->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ $modules->where('is_active', true)->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Modules</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $modules->where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-bold">${{ number_format($modules->sum('sale_price'), 0) }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($modules->sum('sale_price'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-bold">${{ number_format($modules->sum(function($module) { return $module->profit; }), 0) }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Profit</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($modules->sum(function($module) { return $module->profit; }), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 