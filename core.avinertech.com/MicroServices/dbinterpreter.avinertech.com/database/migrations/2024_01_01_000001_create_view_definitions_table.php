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
        Schema::create('view_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->string('schema_name', 64);
            $table->string('table_name', 64);
            $table->string('view_name', 100);
            $table->enum('view_type', ['create', 'update', 'list', 'analytics']);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('cache_key', 255)->unique()->nullable();
            $table->longText('cached_content')->nullable();
            $table->timestamp('cache_expires_at')->nullable();
            $table->bigInteger('schema_version')->default(0);
            $table->enum('rendering_mode', ['dynamic', 'cached', 'hybrid'])->default('hybrid');
            $table->timestamps();

            $table->index(['tenant_id', 'schema_name', 'table_name']);
            $table->index('view_type');
            $table->index('cache_key');
            $table->unique(['tenant_id', 'schema_name', 'table_name', 'view_name', 'view_type'], 'unique_view_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_definitions');
    }
};
