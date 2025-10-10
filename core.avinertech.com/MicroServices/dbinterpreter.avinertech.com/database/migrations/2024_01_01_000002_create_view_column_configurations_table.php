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
        Schema::create('view_column_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('view_definition_id')->constrained()->onDelete('cascade');
            $table->string('column_name', 64);
            $table->string('display_name', 255)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_editable')->default(true);
            $table->boolean('is_required')->default(false);
            $table->boolean('is_searchable')->default(false);
            $table->boolean('is_sortable')->default(true);
            $table->integer('display_order')->default(0);
            $table->integer('column_width')->nullable();
            $table->string('data_type', 50);
            $table->boolean('is_password_field')->default(false);
            $table->json('validation_rules')->nullable();
            $table->string('display_format', 100)->nullable();
            $table->string('placeholder_text', 255)->nullable();
            $table->text('help_text')->nullable();
            $table->timestamps();

            $table->index('view_definition_id');
            $table->index('display_order');
            $table->unique(['view_definition_id', 'column_name'], 'unique_view_column');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('view_column_configurations');
    }
};
