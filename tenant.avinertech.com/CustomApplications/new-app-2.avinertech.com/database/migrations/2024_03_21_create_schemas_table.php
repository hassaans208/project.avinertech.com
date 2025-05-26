<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schemas', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->json('schema');
            $table->enum('table_type', ['regular', 'pivot', 'enum'])->default('regular');
            $table->json('queries')->nullable();
            $table->timestamps();

            // Indexes
            $table->unique(['model_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('schemas');
    }
};