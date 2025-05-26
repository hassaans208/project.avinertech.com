<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Configuration name (e.g., db_name, app_name)');
            $table->text('value')->comment('Configuration value');
            $table->string('type')->default('app')->comment('Configuration type (database, app, mail, etc.)');
            $table->string('host')->comment('Tenant host');
            $table->string('group')->nullable()->comment('Configuration group (e.g., database, security)');
            $table->boolean('is_encrypted')->default(false)->comment('Whether the value is encrypted');
            $table->timestamps();

            // Indexes
            $table->index('host');
            $table->index('type');
            $table->index('group');
            $table->unique(['name', 'type', 'host']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('configurations');
    }
};