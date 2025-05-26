<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('column_types', function (Blueprint $table) {
            $table->id();
            $table->string('mysql_type')->unique();
            $table->string('laravel_method');
            $table->string('parameters')->nullable();
            $table->text('description')->nullable();
            $table->boolean('requires_length')->default(false);
            $table->boolean('requires_precision')->default(false);
            $table->boolean('requires_scale')->default(false);
            $table->boolean('requires_values')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('column_types');
    }
}; 