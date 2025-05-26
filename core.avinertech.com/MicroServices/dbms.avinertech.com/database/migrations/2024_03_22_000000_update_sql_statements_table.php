<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sql_statements', function (Blueprint $table) {
            // Drop existing columns that are being replaced
            // $table->dropColumn(['laravel_migration', 'laravel_query_builder', 'laravel_eloquent']);
            
            // Add new column
            $table->string('laravel_method')->default('')->after('category');
            $table->json('arguments')->default('[]')->after('laravel_method');
        });
    }

    public function down(): void
    {
        Schema::table('sql_statements', function (Blueprint $table) {
            // Remove new column
            $table->dropColumn('laravel_method');
            $table->dropColumn('arguments');
            // Restore original columns
            $table->string('laravel_migration')->nullable();
            $table->string('laravel_query_builder')->nullable();
            $table->string('laravel_eloquent')->nullable();
        });
    }
}; 