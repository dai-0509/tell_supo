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
        Schema::table('call_logs', function (Blueprint $table) {
            // インデックスを削除してからカラムを削除
            $table->dropIndex('call_logs_result_index');
            $table->dropColumn(['result', 'duration_seconds', 'memo']);
        });
        
        Schema::table('call_logs', function (Blueprint $table) {
            // 新しい架電結果enum
            $table->enum('result', ['success', 'no_answer', 'busy', 'appointment', 'not_interested', 'callback'])
                  ->after('called_at')->index();
            
            // 次回架電予定日とnotesフィールドを追加
            $table->date('next_call_date')->nullable()->after('result');
            $table->text('notes')->nullable()->after('next_call_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropColumn(['result', 'next_call_date', 'notes']);
            $table->enum('result', ['connected','no_answer','voicemail','rejected'])->after('called_at');
            $table->integer('duration_seconds')->nullable();
            $table->text('memo')->nullable();
        });
    }
};
