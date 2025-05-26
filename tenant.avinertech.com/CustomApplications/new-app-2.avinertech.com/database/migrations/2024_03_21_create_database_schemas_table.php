<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('database_schemas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('table_type', ['regular', 'pivot', 'enum'])->default('regular');
            $table->json('schema');
            $table->json('queries')->nullable();
            $table->json('migration')->nullable();
            $table->json('model')->nullable();
            $table->json('factory')->nullable();
            $table->json('seeder')->nullable();
            $table->json('relationships')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('database_schemas');
    }
};