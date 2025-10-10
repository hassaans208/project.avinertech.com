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
        Schema::table('view_column_configurations', function (Blueprint $table) {
            $table->json('column_options')->nullable()->after('help_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('view_column_configurations', function (Blueprint $table) {
            $table->dropColumn('column_options');
        });
    }
};