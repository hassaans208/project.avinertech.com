<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SqlStatement extends Model
{
    protected $fillable = [
        'sql_concept',
        'category',
        'laravel_method',
        'arguments',
        'description',
        'example_sql',
        'example_laravel',
        'is_common',
        'complexity_level',
    ];

    protected $casts = [
        'is_common' => 'boolean',
        'complexity_level' => 'integer',
    ];

    /**
     * Get all statements by category
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCategory()
    {
        return self::orderBy('category')
            ->orderBy('complexity_level')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get common statements
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCommon()
    {
        return self::where('is_common', true)
            ->orderBy('category')
            ->get();
    }

    /**
     * Get statements by complexity level
     *
     * @param int $level
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByComplexity(int $level)
    {
        return self::where('complexity_level', $level)
            ->orderBy('category')
            ->get();
    }

    /**
     * Get statements by category
     *
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByCategoryName(string $category)
    {
        return self::where('category', $category)
            ->orderBy('complexity_level')
            ->get();
    }
} 