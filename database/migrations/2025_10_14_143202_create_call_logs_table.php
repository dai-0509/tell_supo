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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->datetime('started_at');
            $table->datetime('ended_at')->nullable();
            $table->enum('result', ['connected', 'no_answer', 'busy', 'failed', 'voicemail']);
            $table->text('notes')->nullable();
            $table->timestamps();

            // インデックス
            $table->index(['user_id', 'started_at']);
            $table->index(['customer_id', 'started_at']);
            $table->index(['result']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
