<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Service Module') }}
            </h2>
            <a href="{{ route('service-modules.index', ['access_token' => $accessToken]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Modules
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('service-modules.store', ['access_token' => $accessToken]) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Module Name (Internal) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                               placeholder="api_access">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Use snake_case format (e.g., api_access, ai_integration)</p>
                    </div>

                    <!-- Display Name -->
                    <div class="md:col-span-2">
                        <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Display Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('display_name') border-red-500 @enderror"
                               placeholder="API Access">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Human-readable name for the module</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                                  placeholder="Brief description of what this module provides...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost Price -->
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="cost_price" id="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('cost_price') border-red-500 @enderror"
                               placeholder="5.00">
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Your cost to provide this module</p>
                    </div>

                    <!-- Sale Price -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Sale Price <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('sale_price') border-red-500 @enderror"
                               placeholder="15.00">
                        @error('sale_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Price you charge customers</p>
                    </div>

                    <!-- Tax Rate -->
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Tax Rate <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', '0.0825') }}" step="0.0001" min="0" max="1" required
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

                    <!-- Currency -->
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">
                            Currency <span class="text-red-500">*</span>
                        </label>
                        <select name="currency" id="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('currency') border-red-500 @enderror">
                            <option value="USD" {{ old('currency', 'USD') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" {{ old('currency') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" {{ old('currency') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="AUD" {{ old('currency') === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Active -->
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 text-sm text-gray-700">
                                Module is active and available for use
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preview -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Module Preview</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Display Name:</span>
                            <span id="preview_display_name" class="ml-2 font-medium">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Internal Name:</span>
                            <span id="preview_name" class="ml-2 font-mono">-</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Cost Price:</span>
                            <span id="preview_cost" class="ml-2 font-medium">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Sale Price:</span>
                            <span id="preview_sale" class="ml-2 font-medium text-green-600">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Tax Amount:</span>
                            <span id="preview_tax" class="ml-2 font-medium">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Total with Tax:</span>
                            <span id="preview_total" class="ml-2 font-bold">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Profit:</span>
                            <span id="preview_profit" class="ml-2 font-medium">$0.00</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span id="preview_status" class="ml-2">Active</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('service-modules.index', ['access_token' => $accessToken]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create Module
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePreview() {
            const displayName = document.getElementById('display_name').value || '-';
            const name = document.getElementById('name').value || '-';
            const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
            const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
            const currency = document.getElementById('currency').value || 'USD';
            const isActive = document.getElementById('is_active').checked;

            const taxAmount = salePrice * taxRate;
            const totalWithTax = salePrice + taxAmount;
            const profit = salePrice - costPrice;

            document.getElementById('preview_display_name').textContent = displayName;
            document.getElementById('preview_name').textContent = name;
            document.getElementById('preview_cost').textContent = `$${costPrice.toFixed(2)} ${currency}`;
            document.getElementById('preview_sale').textContent = `$${salePrice.toFixed(2)} ${currency}`;
            document.getElementById('preview_tax').textContent = `$${taxAmount.toFixed(2)} ${currency}`;
            document.getElementById('preview_total').textContent = `$${totalWithTax.toFixed(2)} ${currency}`;
            document.getElementById('preview_profit').textContent = `$${profit.toFixed(2)} ${currency}`;
            document.getElementById('preview_profit').className = `ml-2 font-medium ${profit >= 0 ? 'text-green-600' : 'text-red-600'}`;
            document.getElementById('preview_status').textContent = isActive ? 'Active' : 'Inactive';
        }

        // Add event listeners
        document.getElementById('display_name').addEventListener('input', updatePreview);
        document.getElementById('name').addEventListener('input', updatePreview);
        document.getElementById('cost_price').addEventListener('input', updatePreview);
        document.getElementById('sale_price').addEventListener('input', updatePreview);
        document.getElementById('tax_rate').addEventListener('input', updatePreview);
        document.getElementById('currency').addEventListener('change', updatePreview);
        document.getElementById('is_active').addEventListener('change', updatePreview);

        // Initial preview
        updatePreview();
    </script>
</x-app-layout> 