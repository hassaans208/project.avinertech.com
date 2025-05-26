@extends('layouts.app')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
                {{ isset($sqlStatement) ? 'Edit SQL Statement' : 'Create SQL Statement' }}
            </h2>
        </div>

        <form action="{{ isset($sqlStatement) ? route('sql-statements.update', $sqlStatement) : route('sql-statements.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($sqlStatement))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SQL Concept -->
                <div>
                    <label for="sql_concept" class="block text-sm font-medium text-gray-700">SQL Concept</label>
                    <input type="text" name="sql_concept" id="sql_concept" 
                        value="{{ old('sql_concept', $sqlStatement->sql_concept ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ (old('category', $sqlStatement->category ?? '') == $category) ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Laravel Method -->
                <div>
                    <label for="laravel_method" class="block text-sm font-medium text-gray-700">Laravel Method</label>
                    <input type="text" name="laravel_method" id="laravel_method" 
                        value="{{ old('laravel_method', $sqlStatement->laravel_method ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <!-- Complexity Level -->
                <div>
                    <label for="complexity_level" class="block text-sm font-medium text-gray-700">Complexity Level</label>
                    <select name="complexity_level" id="complexity_level" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ (old('complexity_level', $sqlStatement->complexity_level ?? '') == $i) ? 'selected' : '' }}>
                                Level {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <!-- Is Common -->
                <div class="flex items-center">
                    <input type="checkbox" name="is_common" id="is_common" value="1"
                        {{ old('is_common', $sqlStatement->is_common ?? false) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_common" class="ml-2 block text-sm text-gray-900">
                        Common Statement
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>{{ old('description', $sqlStatement->description ?? '') }}</textarea>
            </div>

            <!-- Example SQL -->
            <div>
                <label for="example_sql" class="block text-sm font-medium text-gray-700">Example SQL</label>
                <textarea name="example_sql" id="example_sql" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono"
                    required>{{ old('example_sql', $sqlStatement->example_sql ?? '') }}</textarea>
            </div>

            <!-- Example Laravel -->
            <div>
                <label for="example_laravel" class="block text-sm font-medium text-gray-700">Example Laravel</label>
                <textarea name="example_laravel" id="example_laravel" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono"
                    required>{{ old('example_laravel', $sqlStatement->example_laravel ?? '') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('sql-statements.index') }}" 
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ isset($sqlStatement) ? 'Update' : 'Create' }} Statement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 