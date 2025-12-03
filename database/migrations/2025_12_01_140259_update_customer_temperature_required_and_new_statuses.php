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
        // 温度感がnullの既存データにデフォルト値を設定
        DB::table('customers')->whereNull('temperature_rating')->update(['temperature_rating' => 'C']);
        
        // 既存のステータスを新しいステータスにマッピング
        DB::table('customers')->where('status', '新規')->update(['status' => '受けブロ']);
        DB::table('customers')->where('status', '連絡済み')->update(['status' => '会話のみ']);
        DB::table('customers')->where('status', '見込み客')->update(['status' => '見込みあり']);
        DB::table('customers')->where('status', '提案中')->update(['status' => '競合サービス利用中']);
        DB::table('customers')->where('status', '交渉中')->update(['status' => '過去取引あり']);
        DB::table('customers')->where('status', '成約')->update(['status' => '取引中']);

        Schema::table('customers', function (Blueprint $table) {
            // 温度感を必須に変更
            $table->enum('temperature_rating', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable(false)->change();
            
            // ステータスのデフォルト値を変更
            $table->string('status', 30)->default('受けブロ')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 新しいステータスを元のステータスにマッピング
        DB::table('customers')->where('status', '受けブロ')->update(['status' => '新規']);
        DB::table('customers')->where('status', '会話のみ')->update(['status' => '連絡済み']);
        DB::table('customers')->where('status', '見込みあり')->update(['status' => '見込み客']);
        DB::table('customers')->where('status', '競合サービス利用中')->update(['status' => '提案中']);
        DB::table('customers')->where('status', '過去取引あり')->update(['status' => '交渉中']);
        DB::table('customers')->where('status', '取引中')->update(['status' => '成約']);

        Schema::table('customers', function (Blueprint $table) {
            // 温度感を再度nullable可能に
            $table->enum('temperature_rating', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable(true)->change();
            
            // ステータスのデフォルト値を元に戻す
            $table->string('status', 30)->default('新規')->change();
        });
    }
};
