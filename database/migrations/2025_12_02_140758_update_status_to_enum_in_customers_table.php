<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQLの場合、ALTER TABLE文を直接実行
        DB::statement("ALTER TABLE customers MODIFY COLUMN status ENUM('受けブロ', '会話のみ', '見込みあり', '競合サービス利用中', '過去取引あり', '取引中', '架電禁止') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 元のstring型に戻す
        DB::statement("ALTER TABLE customers MODIFY COLUMN status VARCHAR(255) NOT NULL");
    }
};