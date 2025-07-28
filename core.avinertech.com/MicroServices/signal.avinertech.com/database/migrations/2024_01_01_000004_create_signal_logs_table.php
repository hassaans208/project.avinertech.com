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
        Schema::create('signal_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('set null');
            $table->string('encrypted_host_id');
            $table->string('decrypted_host')->nullable();
            $table->text('hash_payload')->nullable();
            $table->string('package_name')->nullable();
            $table->timestamp('signal_timestamp')->nullable();
            $table->string('status'); // success, failed, invalid_tenant, expired, etc.
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('encrypted_host_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_logs');
    }
}; 