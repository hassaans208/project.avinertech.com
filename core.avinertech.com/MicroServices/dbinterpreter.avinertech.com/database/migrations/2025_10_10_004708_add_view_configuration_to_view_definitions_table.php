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
        Schema::table('view_definitions', function (Blueprint $table) {
            $table->json('view_configuration')->nullable()->after('rendering_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('view_definitions', function (Blueprint $table) {
            $table->dropColumn('view_configuration');
        });
    }
};