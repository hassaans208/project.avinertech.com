<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::connection('main')->create('Tenant', function (Blueprint $table) {
            $table->id();
            $table->string('host', 255);
            $table->string('username', 255);
            $table->string('email', 255);
            $table->string('password', 255);
            $table->integer('port')->default(22);
            $table->string('database_host', 255)->default('localhost');
            $table->integer('database_port')->default(3306);
            $table->string('database_name', 255);
            $table->string('database_user', 255);
            $table->string('database_password', 255);
            $table->boolean('is_active')->default(true);
            $table->boolean('paid')->default(true);
            $table->boolean('blocked')->default(false);
            $table->timestamp('last_connection_at')->nullable();
            $table->text('connection_log')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('is_active');
            $table->index('paid');
            $table->index('blocked');
            $table->index('last_connection_at');
        });

        // Add encryption for sensitive fields
        DB::connection('main')->statement('ALTER TABLE Tenant MODIFY password VARBINARY(255)');
        DB::connection('main')->statement('ALTER TABLE Tenant MODIFY database_password VARBINARY(255)');
    }

    public function down()
    {
        Schema::connection('main')->dropIfExists('tenants');
    }
};