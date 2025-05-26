<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('view_stubs', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('view_path')->nullable();
            $table->string('layout')->default('layouts.app');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('fields');
            $table->json('actions')->nullable();
            $table->json('relationships')->nullable();
            $table->json('generated_views')->nullable();
            $table->string('encryption_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['model_name', 'view_path']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('view_stubs');
    }
}; 