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
        // まず既存の英語値を日本語に変換
        DB::table('customers')->where('status', 'new')->update(['status' => '新規']);
        DB::table('customers')->where('status', 'contacted')->update(['status' => '連絡済み']);
        DB::table('customers')->where('status', 'qualified')->update(['status' => '見込み客']);
        DB::table('customers')->where('status', 'proposal')->update(['status' => '提案中']);
        DB::table('customers')->where('status', 'negotiation')->update(['status' => '交渉中']);
        DB::table('customers')->where('status', 'closed_won')->update(['status' => '成約']);
        
        // カラムの定義変更（ENUMではなくSTRINGとして管理）
        Schema::table('customers', function (Blueprint $table) {
            $table->string('status', 30)->default('新規')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 日本語値を英語に戻す
        DB::table('customers')->where('status', '新規')->update(['status' => 'new']);
        DB::table('customers')->where('status', '連絡済み')->update(['status' => 'contacted']);
        DB::table('customers')->where('status', '見込み客')->update(['status' => 'qualified']);
        DB::table('customers')->where('status', '提案中')->update(['status' => 'proposal']);
        DB::table('customers')->where('status', '交渉中')->update(['status' => 'negotiation']);
        DB::table('customers')->where('status', '成約')->update(['status' => 'closed_won']);
        
        Schema::table('customers', function (Blueprint $table) {
            $table->string('status', 30)->default('new')->change();
        });
    }
};
