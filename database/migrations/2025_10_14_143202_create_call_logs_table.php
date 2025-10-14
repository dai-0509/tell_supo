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

        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

        $table->dateTime('called_at');

        $table->enum('outcome', [
            'no_answer',     // 不在
            'talked',        // 会話あり
            'appointment',   // アポ獲得
            'rejected',      // 断り
            'wrong_number'   // 間違い電話
        ])->default('talked');

        $table->unsignedInteger('duration_sec')->nullable(); // 通話秒数
        $table->string('note', 500)->nullable();             // 短いメモ

        $table->timestamps();

        $table->index(['user_id', 'customer_id', 'called_at']);
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
