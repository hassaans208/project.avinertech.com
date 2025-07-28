<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Packages') }}
            </h2>
            <a href="{{ route('packages.create', ['access_token' => $accessToken]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Package
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($packages as $package)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $package->name }}</h3>
                                <p class="text-2xl font-bold text-indigo-600">${{ $package->cost }} {{ $package->currency }}</p>
                                @if($package->tax_rate > 0)
                                    <p class="text-sm text-gray-500">Tax Rate: {{ ($package->tax_rate * 100) }}%</p>
                                @endif
                            </div>
                            @if($package->isFree())
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    FREE
                                </span>
                            @endif
                        </div>

                        <!-- Modules -->
                        @if($package->modules && count($package->modules) > 0)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Modules:</h4>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($package->modules as $module)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                            {{ str_replace('_', ' ', ucfirst($module)) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Tenant Count -->
                        <div class="mb-4">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">{{ $package->tenants->count() }}</span> 
                                {{ Str::plural('tenant', $package->tenants->count()) }} using this package
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="space-x-2">
                                <a href="{{ route('packages.show', ['id' => $package->id, 'access_token' => $accessToken]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('packages.edit', ['id' => $package->id, 'access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Edit
                                </a>
                            </div>
                            <p class="text-xs text-gray-500">
                                Created {{ $package->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500">
                            <p class="text-lg mb-2">No packages found.</p>
                            <a href="{{ route('packages.create', ['access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900">Create your first package</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Package Summary Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                            <span class="text-white text-sm font-bold">{{ $packages->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Packages</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $packages->count() }}</dd>
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
                            <span class="text-white text-sm font-bold">{{ $packages->where('cost', 0)->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Free Packages</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $packages->where('cost', 0)->count() }}</dd>
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
                            <span class="text-white text-sm font-bold">{{ $packages->where('cost', '>', 0)->count() }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Paid Packages</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $packages->where('cost', '>', 0)->count() }}</dd>
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
                            <span class="text-white text-sm font-bold">{{ $packages->sum(function($package) { return $package->tenants->count(); }) }}</span>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Subscriptions</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $packages->sum(function($package) { return $package->tenants->count(); }) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 