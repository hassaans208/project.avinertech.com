@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Create New Tenant</h1>
        <a href="{{ route('tenants.index') }}" class="text-blue-500 hover:text-blue-700">
            &larr; Back to Tenants
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <div class="font-bold">Please fix the following errors:</div>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Tenant Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('tenants.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Tenant Information Section -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Tenant Information</h2>
                </div>
                
                <div>
                    <label for="tenant_url" class="block text-sm font-medium text-gray-700 mb-1">
                        Tenant URL <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tenant_url" id="tenant_url" value="{{ old('tenant_url') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required placeholder="company-name">
                    <p class="text-xs text-gray-500 mt-1">Only lowercase letters, numbers, and hyphens allowed.</p>
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="2" 
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                </div>

                <!-- Module Configuration Section -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Module Configuration</h2>
                </div>
                
                <div>
                    <label for="module" class="block text-sm font-medium text-gray-700 mb-1">
                        Module Type <span class="text-red-500">*</span>
                    </label>
                    <select name="module" id="module" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <option value="core" {{ old('module') == 'core' ? 'selected' : '' }}>Core</option>
                        <option value="tenant" {{ old('module') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                    </select>
                </div>

                <div>
                    <label for="submodule" class="block text-sm font-medium text-gray-700 mb-1">
                        Submodule Type <span class="text-red-500">*</span>
                    </label>
                    <select name="submodule" id="submodule" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <optgroup label="Core Submodules">
                            <option value="micro" {{ old('submodule') == 'micro' ? 'selected' : '' }}>Microservice</option>
                            <option value="service" {{ old('submodule') == 'service' ? 'selected' : '' }}>Service</option>
                            <option value="basic" {{ old('submodule') == 'basic' ? 'selected' : '' }}>Basic</option>
                        </optgroup>
                        <optgroup label="Tenant Submodules">
                            <option value="app" {{ old('submodule') == 'app' ? 'selected' : '' }}>Application</option>
                            <option value="custom-app" {{ old('submodule') == 'custom-app' ? 'selected' : '' }}>Custom Application</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Database Configuration Section -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Database Configuration</h2>
                </div>
                
                <div>
                    <label for="database_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Database Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="database_name" id="database_name" value="{{ old('database_name') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">Only lowercase letters, numbers, and underscores allowed.</p>
                </div>

                <div>
                    <label for="database_user" class="block text-sm font-medium text-gray-700 mb-1">
                        Database User <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="database_user" id="database_user" value="{{ old('database_user') }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                    <p class="text-xs text-gray-500 mt-1">Only lowercase letters, numbers, and underscores allowed.</p>
                </div>

                <div class="col-span-2">
                    <label for="database_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Database Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="database_password" id="database_password" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required minlength="8">
                    <p class="text-xs text-gray-500 mt-1">Minimum 8 characters.</p>
                </div>
            </div>

            <!-- Action Buttons Section -->
            <div class="mt-8 flex flex-col space-y-4">
                <button type="submit" name="action" value="create_tenant" 
                        class="w-full px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-md font-medium">
                    Create Tenant
                </button>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button type="submit" name="action" value="create_module" 
                            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md">
                        Create Module
                    </button>
                    <button type="submit" name="action" value="deploy_module" 
                            class="px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-md">
                        Deploy Module
                    </button>
                    <button type="submit" name="action" value="ssl_cert" 
                            class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-md">
                        Install SSL
                    </button>
                    <button type="submit" name="action" value="create_database" 
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md">
                        Create Database
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module');
    const submoduleSelect = document.getElementById('submodule');
    
    function updateSubmoduleOptions() {
        const coreOptions = submoduleSelect.querySelectorAll('optgroup[label="Core Submodules"] option');
        const tenantOptions = submoduleSelect.querySelectorAll('optgroup[label="Tenant Submodules"] option');
        
        if (moduleSelect.value === 'core') {
            coreOptions.forEach(option => option.style.display = 'block');
            tenantOptions.forEach(option => option.style.display = 'none');
            
            // Select first visible option if current is hidden
            if (submoduleSelect.selectedOptions[0].style.display === 'none') {
                coreOptions[0].selected = true;
            }
        } else {
            coreOptions.forEach(option => option.style.display = 'none');
            tenantOptions.forEach(option => option.style.display = 'block');
            
            // Select first visible option if current is hidden
            if (submoduleSelect.selectedOptions[0].style.display === 'none') {
                tenantOptions[0].selected = true;
            }
        }
    }
    
    // Initial setup
    updateSubmoduleOptions();
    
    // Listen for changes
    moduleSelect.addEventListener('change', updateSubmoduleOptions);
});
</script>
@endsection 