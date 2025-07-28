<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Package') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('packages.show', ['id' => $package->id, 'access_token' => $accessToken]) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    View Details
                </a>
                <a href="{{ route('packages.index', ['access_token' => $accessToken]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Packages
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('packages.update', ['id' => $package->id, 'access_token' => $accessToken]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Package Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $package->name) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                               placeholder="basic_package">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Use snake_case format (e.g., basic_package, premium_plan)</p>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost', $package->cost) }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('cost') border-red-500 @enderror"
                               placeholder="29.99">
                        @error('cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" id="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-500 @enderror">
                            <option value="USD" {{ old('currency', $package->currency) === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency', $package->currency) === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency', $package->currency) === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ old('currency', $package->currency) === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="AUD" {{ old('currency', $package->currency) === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Rate -->
                    <div class="md:col-span-2">
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Rate <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $package->tax_rate) }}" step="0.0001" min="0" max="1" required
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('tax_rate') border-red-500 @enderror"
                                   placeholder="0.0825">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">%</span>
                            </div>
                        </div>
                        @error('tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Enter as decimal (e.g., 0.0825 for 8.25%)</p>
                    </div>
                </div>

                <!-- Modules -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Package Modules
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach($availableModules as $module)
                            <div class="flex items-center">
                                <input type="checkbox" name="modules[]" value="{{ $module }}" id="module_{{ $module }}"
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                       {{ in_array($module, old('modules', $package->modules ?? [])) ? 'checked' : '' }}>
                                <label for="module_{{ $module }}" class="ml-2 text-sm text-gray-700">
                                    {{ str_replace('_', ' ', ucwords($module)) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('modules')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Select the modules/features included in this package</p>
                </div>

                <!-- Current Subscriptions Warning -->
                @if($package->tenants->count() > 0)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Active Subscriptions Warning
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>This package currently has <strong>{{ $package->tenants->count() }} active subscription(s)</strong>. Changes to pricing or modules will affect these tenants immediately.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Preview -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Package Preview</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Name:</span>
                            <span id="preview_name" class="ml-2 font-medium">{{ $package->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Cost:</span>
                            <span id="preview_cost" class="ml-2 font-medium">${{ $package->cost }} {{ $package->currency }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tax Rate:</span>
                            <span id="preview_tax" class="ml-2 font-medium">{{ ($package->tax_rate * 100) }}%</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Modules:</span>
                            <span id="preview_modules" class="ml-2">
                                @if($package->modules && count($package->modules) > 0)
                                    {{ implode(', ', array_map(fn($m) => ucwords(str_replace('_', ' ', $m)), $package->modules)) }}
                                @else
                                    None selected
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('packages.show', ['id' => $package->id, 'access_token' => $accessToken]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Package
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePreview() {
            const name = document.getElementById('name').value || '-';
            const cost = document.getElementById('cost').value || '0';
            const currency = document.getElementById('currency').value || 'USD';
            const taxRate = document.getElementById('tax_rate').value || '0';
            
            const checkedModules = Array.from(document.querySelectorAll('input[name="modules[]"]:checked'))
                .map(cb => cb.value.replace('_', ' '))
                .map(module => module.charAt(0).toUpperCase() + module.slice(1));

            document.getElementById('preview_name').textContent = name;
            document.getElementById('preview_cost').textContent = `$${cost} ${currency}`;
            document.getElementById('preview_tax').textContent = `${(parseFloat(taxRate) * 100).toFixed(2)}%`;
            document.getElementById('preview_modules').textContent = checkedModules.length > 0 ? checkedModules.join(', ') : 'None selected';
        }

        // Add event listeners
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('cost').addEventListener('input', updatePreview);
        document.getElementById('currency').addEventListener('change', updatePreview);
        document.getElementById('tax_rate').addEventListener('input', updatePreview);
        
        document.querySelectorAll('input[name="modules[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updatePreview);
        });

        // Initial preview is already set from server data
    </script>
</x-app-layout> 