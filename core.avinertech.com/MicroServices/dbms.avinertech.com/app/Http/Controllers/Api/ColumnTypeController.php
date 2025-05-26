<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ColumnType;
use Illuminate\Http\Request;
use App\Services\QueryInterpreterService;

class ColumnTypeController extends Controller
{
    public function index()
    {
        return ColumnType::all();
    }

    public function show($id)
    {
        return ColumnType::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mysql_type' => 'required|string|unique:column_types,mysql_type',
            'laravel_method' => 'required|string',
            'parameters' => 'nullable|string',
            'description' => 'nullable|string',
            'requires_length' => 'boolean',
            'requires_precision' => 'boolean',
            'requires_scale' => 'boolean',
            'requires_values' => 'boolean',
        ]);
        return ColumnType::create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'mysql_type' => 'sometimes|required|string|unique:column_types,mysql_type,' . $id,
            'laravel_method' => 'sometimes|required|string',
            'parameters' => 'nullable|string',
            'description' => 'nullable|string',
            'requires_length' => 'boolean',
            'requires_precision' => 'boolean',
            'requires_scale' => 'boolean',
            'requires_values' => 'boolean',
        ]);
        $columnType = ColumnType::findOrFail($id);
        $columnType->update($data);
        return $columnType;
    }

    public function destroy($id)
    {
        $columnType = ColumnType::findOrFail($id);
        $columnType->delete();
        return response()->json(['success' => true]);
    }
} 