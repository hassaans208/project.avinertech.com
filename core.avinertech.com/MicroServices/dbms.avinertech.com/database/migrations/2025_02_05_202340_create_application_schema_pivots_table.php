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
        Schema::create('application_schema_pivots', function (Blueprint $table) {
            $table->foreignId('schema_id')->constrained('schemas');
            $table->foreignId('application_id')->constrained('applications');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_schema_pivots');
    }
};
