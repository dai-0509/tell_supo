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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();     // 発信担当
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete(); // 相手
            $table->timestamp('called_at')->index();                             // 架電日時
            $table->enum('result', ['connected','no_answer','voicemail','rejected'])
                  ->index();                                                     // 結果
            $table->integer('duration_seconds')->nullable();                     // 通話時間
            $table->text('memo')->nullable();                                    // メモ
            $table->timestamps();
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
