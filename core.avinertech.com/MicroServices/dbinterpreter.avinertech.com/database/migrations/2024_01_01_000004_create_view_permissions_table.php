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
        Schema::create('view_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('view_definition_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->enum('permission_type', ['read', 'write', 'admin']);
            $table->unsignedBigInteger('granted_by');
            $table->timestamp('granted_at')->useCurrent();

            $table->index('view_definition_id');
            $table->index('user_id');
            $table->index('role_id');
            $table->unique(['view_definition_id', 'user_id', 'permission_type'], 'unique_user_permission');
            $table->unique(['view_definition_id', 'role_id', 'permission_type'], 'unique_role_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): voidnp
    {
        Schema::dropIfExists('view_permissions');
    }
};
