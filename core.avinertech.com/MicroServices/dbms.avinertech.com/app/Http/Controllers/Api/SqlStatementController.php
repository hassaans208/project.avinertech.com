<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SqlStatement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class SqlStatementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SqlStatement::query();

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by complexity level
        if ($request->has('complexity_level')) {
            $query->where('complexity_level', $request->complexity_level);
        }

        // Filter by common statements
        if ($request->has('is_common')) {
            $query->where('is_common', $request->boolean('is_common'));
        }

        // Search by concept or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sql_concept', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('laravel_method', 'like', "%{$search}%");
            });
        }

        $statements = $query->paginate($request->input('per_page', 15));

        return response()->json($statements);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sql_concept' => 'required|string|max:255|unique:sql_statements',
            'category' => 'required|string|max:50',
            'laravel_method' => 'required|string|max:255',
            'description' => 'required|string',
            'example_sql' => 'required|string',
            'example_laravel' => 'required|string',
            'is_common' => 'boolean',
            'complexity_level' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $statement = SqlStatement::create($request->all());

        return response()->json($statement, 201);
    }

    public function show(SqlStatement $sqlStatement): JsonResponse
    {
        return response()->json($sqlStatement);
    }

    public function update(Request $request, SqlStatement $sqlStatement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sql_concept' => 'string|max:255|unique:sql_statements,sql_concept,' . $sqlStatement->id,
            'category' => 'string|max:50',
            'laravel_method' => 'string|max:255',
            'description' => 'string',
            'example_sql' => 'string',
            'example_laravel' => 'string',
            'is_common' => 'boolean',
            'complexity_level' => 'integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sqlStatement->update($request->all());

        return response()->json($sqlStatement);
    }

    public function destroy(SqlStatement $sqlStatement): JsonResponse
    {
        $sqlStatement->delete();

        return response()->json(null, 204);
    }

    public function categories(): JsonResponse
    {
        $categories = SqlStatement::distinct()->pluck('category');

        return response()->json($categories);
    }
} 