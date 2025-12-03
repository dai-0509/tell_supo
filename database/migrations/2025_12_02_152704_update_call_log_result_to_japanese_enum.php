<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 既存データをクリア（テスト環境のため）
        DB::table('call_logs')->truncate();
        
        // resultカラムをenum型に変更
        DB::statement("ALTER TABLE call_logs MODIFY COLUMN result ENUM('通話成功', '受けブロ', '会話のみ', '見込みあり') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 既存データをクリア
        DB::table('call_logs')->truncate();
        
        // 元のenum値に戻す
        DB::statement("ALTER TABLE call_logs MODIFY COLUMN result ENUM('connected', 'no_answer', 'busy', 'failed', 'voicemail') NOT NULL");
    }
};
