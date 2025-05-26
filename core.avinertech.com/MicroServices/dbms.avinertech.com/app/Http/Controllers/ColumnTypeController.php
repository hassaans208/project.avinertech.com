<?php

namespace App\Http\Controllers;

use App\Models\ColumnType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ColumnTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ColumnType::query();

        // Filter by database type
        if ($request->has('mysql_type')) {
            $query->where('mysql_type', $request->mysql_type);
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $columnTypes = $query->paginate(15);
        $databaseTypes = ColumnType::distinct()->pluck('mysql_type');

        return view('column-types.index', compact('columnTypes', 'databaseTypes'));
    }

    public function create()
    {
        $databaseTypes = ColumnType::distinct()->pluck('mysql_type');
        return view('column-types.create', compact('databaseTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:column_types',
            'mysql_type' => 'required|string|max:50',
            'description' => 'required|string',
            'laravel_type' => 'required|string|max:255',
            'example' => 'required|string',
            'is_common' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        ColumnType::create($request->all());

        return redirect()->route('column-types.index')
            ->with('success', 'Column type created successfully.');
    }

    public function edit(ColumnType $columnType)
    {
        $databaseTypes = ColumnType::distinct()->pluck('mysql_type');
        return view('column-types.edit', compact('columnType', 'databaseTypes'));
    }

    public function update(Request $request, ColumnType $columnType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|unique:column_types,name,' . $columnType->id,
            'mysql_type' => 'string|max:50',
            'description' => 'string',
            'laravel_type' => 'string|max:255',
            'example' => 'string',
            'is_common' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $columnType->update($request->all());

        return redirect()->route('column-types.index')
            ->with('success', 'Column type updated successfully.');
    }

    public function destroy(ColumnType $columnType)
    {
        $columnType->delete();

        return redirect()->route('column-types.index')
            ->with('success', 'Column type deleted successfully.');
    }
} 