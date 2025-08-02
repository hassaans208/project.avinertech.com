@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit Tenant: {{ $tenant->tenant_url }}</h1>
        <a href="{{ route('tenants.index') }}" class="text-blue-500 hover:text-blue-700">
            &larr; Back to Tenants
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

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

    <!-- Edit Tenant Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('tenants.update', $tenant) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Tenant Information Section -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Tenant Information</h2>
                </div>
                
                <div>
                    <label for="tenant_url" class="block text-sm font-medium text-gray-700 mb-1">
                        Tenant URL
                    </label>
                    <input type="text" id="tenant_url" value="{{ $tenant->tenant_url }}" 
                           class="w-full px-3 py-2 border rounded-md bg-gray-100" 
                           disabled>
                    <p class="text-xs text-gray-500 mt-1">Tenant URL cannot be changed.</p>
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Company Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $tenant->company_name) }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $tenant->phone) }}" 
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>

                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Address
                    </label>
                    <textarea name="address" id="address" rows="2" 
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $tenant->address) }}</textarea>
                </div>

                <!-- Status Settings Section -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Status Settings</h2>
                </div>

                <div class="col-span-2 space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_paid" id="is_paid" value="1" 
                               {{ old('is_paid', $tenant->is_paid) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_paid" class="ml-2 block text-sm text-gray-900">
                            Paid
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_blocked" id="is_blocked" value="1" 
                               {{ old('is_blocked', $tenant->is_blocked) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_blocked" class="ml-2 block text-sm text-gray-900">
                            Blocked
                        </label>
                    </div>
                </div>

                <!-- Module Configuration Section (Read-only) -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Module Configuration</h2>
                </div>
                
                <div>
                    <label for="module" class="block text-sm font-medium text-gray-700 mb-1">
                        Module Type
                    </label>
                    <input type="text" id="module" value="{{ $tenant->module }}" 
                           class="w-full px-3 py-2 border rounded-md bg-gray-100" 
                           disabled>
                </div>

                <div>
                    <label for="submodule" class="block text-sm font-medium text-gray-700 mb-1">
                        Submodule Type
                    </label>
                    <input type="text" id="submodule" value="{{ $tenant->submodule }}" 
                           class="w-full px-3 py-2 border rounded-md bg-gray-100" 
                           disabled>
                </div>

                <!-- Database Configuration Section (Read-only) -->
                <div class="col-span-2">
                    <h2 class="text-lg font-medium border-b pb-2 mb-4">Database Configuration</h2>
                </div>
                
                <div>
                    <label for="database_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Database Name
                    </label>
                    <input type="text" id="database_name" value="{{ $tenant->database_name }}" 
                           class="w-full px-3 py-2 border rounded-md bg-gray-100" 
                           disabled>
                </div>

                <div>
                    <label for="database_user" class="block text-sm font-medium text-gray-700 mb-1">
                        Database User
                    </label>
                    <input type="text" id="database_user" value="{{ $tenant->database_user }}" 
                           class="w-full px-3 py-2 border rounded-md bg-gray-100" 
                           disabled>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between pt-4 border-t">
                <div>
                    <a href="{{ route('tenants.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                        Update Tenant
                    </button>
                </div>
            </div>
        </form>

        <!-- Deployment Actions -->
        <div class="mt-10 pt-6 border-t">
            <h2 class="text-lg font-medium mb-4">Deployment Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <form action="{{ route('tenants.create-module', $tenant) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md">
                        Create Module
                    </button>
                </form>
                
                <form action="{{ route('tenants.deploy-module', $tenant) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-md">
                        Deploy Module
                    </button>
                </form>
                
                <form action="{{ route('tenants.ssl-cert', $tenant) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-md">
                        Install SSL
                    </button>
                </form>
                
                <form action="{{ route('tenants.create-database', $tenant) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md">
                        Create Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 