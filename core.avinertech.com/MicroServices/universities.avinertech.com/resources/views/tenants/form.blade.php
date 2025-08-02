@props(['tenant' => null])

<form method="POST" action="{{ $tenant ? route('tenants.update', ['tenant' => $tenant->id, 'token' => request()->token]) : route('tenants.store', ['token' => request()->token]) }}" class="space-y-6">
    @csrf
    @if($tenant)
        @method('PUT')
    @endif

    <div>
        <label for="tenant_name" class="block text-sm font-medium text-gray-700">Tenant Name</label>
        <input type="text" name="tenant_name" id="tenant_name" value="{{ old('tenant_name', $tenant?->tenant_name) }}" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
        @error('tenant_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tenant_url" class="block text-sm font-medium text-gray-700">Tenant URL</label>
        <input type="url" name="tenant_url" id="tenant_url" value="{{ old('tenant_url', $tenant?->tenant_url) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
        @error('tenant_url')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="application_path" class="block text-sm font-medium text-gray-700">Application Path</label>
        <input type="text" name="application_path" id="application_path" value="{{ old('application_path', $tenant?->application_path) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            required>
        @error('application_path')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if(!$tenant)
        <div>
            <label for="module" class="block text-sm font-medium text-gray-700">Module Type</label>
            <select name="module" id="module" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Select Module</option>
                <option value="core" {{ old('module') === 'core' ? 'selected' : '' }}>Core</option>
                <option value="tenant" {{ old('module') === 'tenant' ? 'selected' : '' }}>Tenant</option>
            </select>
            @error('module')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="submodule" class="block text-sm font-medium text-gray-700">Submodule Type</label>
            <select name="submodule" id="submodule" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Select Submodule</option>
                <option value="micro" {{ old('submodule') === 'micro' ? 'selected' : '' }}>Micro Service</option>
                <option value="service" {{ old('submodule') === 'service' ? 'selected' : '' }}>Service</option>
                <option value="apps" {{ old('submodule') === 'apps' ? 'selected' : '' }}>Application</option>
            </select>
            @error('submodule')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    @endif

    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Payment Status</label>
        <select name="status" id="status" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="paid" {{ old('status', $tenant?->status) === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="unpaid" {{ old('status', $tenant?->status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
        </select>
        @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="block_status" class="block text-sm font-medium text-gray-700">Block Status</label>
        <select name="block_status" id="block_status" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="unblocked" {{ old('block_status', $tenant?->block_status) === 'unblocked' ? 'selected' : '' }}>Unblocked</option>
            <option value="blocked" {{ old('block_status', $tenant?->block_status) === 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
        @error('block_status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end space-x-3">
        <a href="{{ route('tenants.index', ['token' => request()->token]) }}" 
            class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Cancel
        </a>
        <button type="submit" 
            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            {{ $tenant ? 'Update' : 'Create' }} Tenant
        </button>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module');
    const submoduleSelect = document.getElementById('submodule');
    
    if (moduleSelect && submoduleSelect) {
        moduleSelect.addEventListener('change', function() {
            // Clear current options
            submoduleSelect.innerHTML = '<option value="">Select Submodule</option>';
            
            // Add options based on selected module
            if (this.value === 'core') {
                submoduleSelect.innerHTML += `
                    <option value="micro">Micro Service</option>
                    <option value="service">Service</option>
                `;
            } else if (this.value === 'tenant') {
                submoduleSelect.innerHTML += `
                    <option value="apps">Application</option>
                `;
            }
        });
    }
});
</script>
@endpush 