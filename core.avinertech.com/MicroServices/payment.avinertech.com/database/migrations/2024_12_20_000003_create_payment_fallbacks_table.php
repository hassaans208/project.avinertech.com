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
        Schema::create('payment_fallbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('payment_transactions')->onDelete('cascade');
            $table->foreignId('method_id')->constrained('payment_methods')->onDelete('cascade');
            $table->integer('attempt_order'); // 1, 2, 3... for fallback sequence
            $table->enum('status', [
                'pending',
                'attempted',
                'succeeded',
                'failed',
                'skipped'
            ])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('tried_at')->nullable();
            $table->timestamps();

            $table->unique(['transaction_id', 'method_id', 'attempt_order']);
            $table->index(['transaction_id', 'attempt_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_fallbacks');
    }
}; 