<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('view_type_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('view_type_id')->constrained('view_types')->onDelete('cascade');
            $table->string('option_key'); // e.g., 'show_encrypted', 'substr', 'hide', 'sortable', 'searchable'
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('option_type')->default('boolean'); // 'boolean', 'string', 'number', 'array', 'object'
            $table->json('default_value')->nullable(); // Default value for this option
            $table->json('validation_rules')->nullable(); // Validation rules for the option
            $table->json('possible_values')->nullable(); // For select/radio options
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['view_type_id', 'option_key']);
            $table->index(['view_type_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_type_options');
    }
};