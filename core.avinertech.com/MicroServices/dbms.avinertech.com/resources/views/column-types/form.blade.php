@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ isset($columnType) ? 'Edit Column Type' : 'Create Column Type' }}
            </h2>
        </div>

        <form action="{{ isset($columnType) ? route('column-types.update', $columnType) : route('column-types.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($columnType))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" 
                        value="{{ old('name', $columnType->name ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <!-- Database Type -->
                <div>
                    <label for="database_type" class="block text-sm font-medium text-gray-700">Database Type</label>
                    <select name="database_type" id="database_type" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Select a database type</option>
                        @foreach($databaseTypes as $type)
                            <option value="{{ $type }}" {{ (old('database_type', $columnType->database_type ?? '') == $type) ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Laravel Type -->
                <div>
                    <label for="laravel_type" class="block text-sm font-medium text-gray-700">Laravel Type</label>
                    <input type="text" name="laravel_type" id="laravel_type" 
                        value="{{ old('laravel_type', $columnType->laravel_type ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <!-- Is Common -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_common" id="is_common" value="1"
                        {{ old('is_common', $columnType->is_common ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_common" class="ml-2 block text-sm text-gray-900">
                        Common Type
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>{{ old('description', $columnType->description ?? '') }}</textarea>
            </div>

            <!-- Example -->
            <div>
                <label for="example" class="block text-sm font-medium text-gray-700">Example</label>
                <textarea name="example" id="example" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono"
                    required>{{ old('example', $columnType->example ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('column-types.index') }}" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ isset($columnType) ? 'Update' : 'Create' }} Column Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 