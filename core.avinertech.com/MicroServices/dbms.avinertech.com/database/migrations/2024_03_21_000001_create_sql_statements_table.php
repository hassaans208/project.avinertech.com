<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sql_statements', function (Blueprint $table) {
            $table->id();
            $table->string('sql_concept')->unique();
            $table->string('category');
            $table->string('laravel_migration')->nullable();
            $table->string('laravel_query_builder')->nullable();
            $table->string('laravel_eloquent')->nullable();
            $table->text('description')->nullable();
            $table->text('example_sql')->nullable();
            $table->text('example_laravel')->nullable();
            $table->boolean('is_common')->default(false);
            $table->integer('complexity_level')->default(1); // 1-5
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sql_statements');
    }
}; 