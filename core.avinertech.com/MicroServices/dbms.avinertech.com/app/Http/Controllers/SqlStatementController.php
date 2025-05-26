<?php

namespace App\Http\Controllers;

use App\Models\SqlStatement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SqlStatementController extends Controller
{
    public function index(Request $request)
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

        $statements = $query->paginate(15);
        $categories = SqlStatement::distinct()->pluck('category');
        $complexityLevels = range(1, 5);

        return view('sql-statements.index', compact('statements', 'categories', 'complexityLevels'));
    }

    public function create()
    {
        $categories = SqlStatement::distinct()->pluck('category');
        return view('sql-statements.create', compact('categories'));
    }

    public function store(Request $request)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        SqlStatement::create($request->all());

        return redirect()->route('sql-statements.index')
            ->with('success', 'SQL statement created successfully.');
    }

    public function edit(SqlStatement $sqlStatement)
    {
        $categories = SqlStatement::distinct()->pluck('category');
        return view('sql-statements.edit', compact('sqlStatement', 'categories'));
    }

    public function update(Request $request, SqlStatement $sqlStatement)
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $sqlStatement->update($request->all());

        return redirect()->route('sql-statements.index')
            ->with('success', 'SQL statement updated successfully.');
    }

    public function destroy(SqlStatement $sqlStatement)
    {
        $sqlStatement->delete();

        return redirect()->route('sql-statements.index')
            ->with('success', 'SQL statement deleted successfully.');
    }
} 