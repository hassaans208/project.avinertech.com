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
        Schema::create('view_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'list', 'create/update', 'analytics', 'soft-delete'
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // For UI display
            $table->string('color')->nullable(); // For UI theming
            $table->json('default_config')->nullable(); // Default configuration for this view type
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_types');
    }
};