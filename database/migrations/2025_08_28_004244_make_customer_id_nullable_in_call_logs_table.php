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
            // 外部キー制約を削除
            $table->dropForeign(['customer_id']);
            
            // customer_idをnullableに変更
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            
            // 外部キー制約を再作成（NULLを許可）
            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['customer_id']);
            
            // customer_idをNOT NULLに戻す
            $table->unsignedBigInteger('customer_id')->nullable(false)->change();
            
            // 外部キー制約を再作成
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }
};