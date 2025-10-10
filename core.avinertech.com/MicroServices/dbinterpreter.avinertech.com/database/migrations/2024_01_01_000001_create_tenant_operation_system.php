<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Operation Cases Table
        Schema::create('operation_cases', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->enum('execution_mode', ['batch', 'instant']);
            $table->boolean('requires_approval')->default(false);
            $table->integer('max_batch_size')->default(50);
            $table->timestamps();
        });

        // Operation Groups (for batch processing)
        Schema::create('operation_groups', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->unsignedBigInteger('case_id');
            $table->string('name', 100);
            $table->enum('status', [
                'draft', 
                'pending_approval', 
                'approved', 
                'queued', 
                'running', 
                'completed', 
                'failed', 
                'cancelled'
            ])->default('draft');
            $table->timestamp('approval_requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('case_id')->references('id')->on('operation_cases');
        });

        // Enhanced Operations Table with Tenant Support
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable(); // NULL for instant operations
            $table->string('tenant_id', 36); // From signature
            $table->unsignedBigInteger('case_id');
            $table->string('type', 50);
            $table->string('name', 100); // Auto-generated
            $table->string('schema_name', 64);
            $table->string('table_name', 64)->nullable();
            $table->json('payload');
            $table->text('sql_preview');
            $table->enum('status', [
                'draft', 
                'pending_approval', 
                'queued', 
                'running', 
                'success', 
                'failed', 
                'cancelled'
            ])->default('draft');
            $table->integer('execution_order')->default(0);
            $table->json('result')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('group_id')->references('id')->on('operation_groups');
            $table->foreign('case_id')->references('id')->on('operation_cases');
        });

        // Tenant Security Logs
        Schema::create('tenant_security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->string('operation_type', 50);
            $table->text('denied_query');
            $table->string('reason', 255);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // Soft Delete Management
        Schema::create('soft_delete_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id', 36);
            $table->string('table_name', 64);
            $table->string('record_id', 36);
            $table->json('original_data');
            $table->timestamp('deleted_at')->useCurrent();
            $table->string('deleted_by', 36); // User/tenant who deleted
            $table->string('signature', 255);
        });

        // Dynamic Query Limits Configuration
        Schema::create('query_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->string('operation_type', 50);
            $table->integer('max_columns_threshold')->default(3);
            $table->integer('max_records_small_table')->default(1000);
            $table->integer('max_records_large_table')->default(100);
            $table->boolean('soft_delete_enabled')->default(true);
            $table->timestamps();
            
            $table->foreign('case_id')->references('id')->on('operation_cases');
            $table->unique(['case_id', 'operation_type']);
        });

        // Signature Verification Log
        Schema::create('signature_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operation_id');
            $table->string('signature', 255);
            $table->enum('verification_status', ['pending', 'verified', 'failed'])->default('pending');
            $table->json('verification_response')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->foreign('operation_id')->references('id')->on('operations');
        });

        // Insert default operation cases
        DB::table('operation_cases')->insert([
            [
                'id' => 1,
                'name' => 'Fresh App DB',
                'description' => 'Building a new application database',
                'execution_mode' => 'batch',
                'requires_approval' => true,
                'max_batch_size' => 50,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'name' => 'Modify/Enhance App DB',
                'description' => 'Altering existing application database',
                'execution_mode' => 'batch',
                'requires_approval' => true,
                'max_batch_size' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'name' => 'Data Operations',
                'description' => 'Using the application for data management',
                'execution_mode' => 'instant',
                'requires_approval' => false,
                'max_batch_size' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insert default query limits
        DB::table('query_limits')->insert([
            [
                'case_id' => 3,
                'operation_type' => 'select',
                'max_columns_threshold' => 3,
                'max_records_small_table' => 1000,
                'max_records_large_table' => 100,
                'soft_delete_enabled' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('signature_verifications');
        Schema::dropIfExists('query_limits');
        Schema::dropIfExists('soft_delete_logs');
        Schema::dropIfExists('tenant_security_logs');
        Schema::dropIfExists('operations');
        Schema::dropIfExists('operation_groups');
        Schema::dropIfExists('operation_cases');
    }
};
