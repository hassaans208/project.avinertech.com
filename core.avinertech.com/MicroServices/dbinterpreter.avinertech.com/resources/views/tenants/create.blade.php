<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Tenant') }}
            </h2>
            <a href="{{ route('tenants.index', ['access_token' => $accessToken]) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Tenants
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <form action="{{ route('tenants.store', ['access_token' => $accessToken]) }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Tenant Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter tenant name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Host -->
                    <div>
                        <label for="host" class="block text-sm font-medium text-gray-700 mb-2">
                            Host <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="host" id="host" value="{{ old('host') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('host') border-red-500 @enderror"
                               placeholder="example.com">
                        @error('host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Enter the domain/host for this tenant</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="blocked" {{ old('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Package -->
                    <div>
                        <label for="package_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Initial Package
                        </label>
                        <select name="package_id" id="package_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('package_id') border-red-500 @enderror">
                            <option value="">Select a package (optional)</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} - ${{ $package->cost }} {{ $package->currency }}
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">If no package is selected, the free package will be assigned automatically</p>
                    </div>
                </div>

                <!-- Block Reason (shown when status is blocked) -->
                <div id="block_reason_field" class="mt-6" style="display: none;">
                    <label for="block_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Block Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea name="block_reason" id="block_reason" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('block_reason') border-red-500 @enderror"
                              placeholder="Enter reason for blocking this tenant...">{{ old('block_reason') }}</textarea>
                    @error('block_reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('tenants.index', ['access_token' => $accessToken]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            const blockReasonField = document.getElementById('block_reason_field');
            const blockReasonInput = document.getElementById('block_reason');
            
            if (this.value === 'blocked') {
                blockReasonField.style.display = 'block';
                blockReasonInput.required = true;
            } else {
                blockReasonField.style.display = 'none';
                blockReasonInput.required = false;
                blockReasonInput.value = '';
            }
        });

        // Check initial state
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            if (statusSelect.value === 'blocked') {
                document.getElementById('block_reason_field').style.display = 'block';
                document.getElementById('block_reason').required = true;
            }
        });
    </script>
</x-app-layout> 