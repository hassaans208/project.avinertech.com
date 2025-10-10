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
        Schema::create('view_layout_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('view_definition_id')->constrained()->onDelete('cascade');
            $table->enum('layout_type', ['form', 'table', 'grid', 'card', 'dashboard']);
            $table->json('layout_config');
            $table->json('responsive_config')->nullable();
            $table->json('theme_config')->nullable();
            $table->timestamps();

            $table->index('view_definition_id');
            $table->index('layout_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_layout_configurations');
    }
};
