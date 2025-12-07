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
        Schema::create('user_kpi_targets', function (Blueprint $table) {
            $table->id('kpi_target_id')->comment('KPI目標ID');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ユーザーID');
            
            // 架電目標（必須）
            $table->unsignedInteger('daily_call_target')->comment('日次目標架電数');
            $table->unsignedInteger('weekly_call_target')->comment('週次目標架電数');
            $table->unsignedInteger('monthly_call_target')->comment('月次目標架電数');
            
            // アポ獲得目標
            $table->unsignedInteger('monthly_appointment_target')->default(0)->comment('月次目標アポ獲得数');
            
            // 成功率目標（パーセント）
            $table->decimal('target_success_rate', 5, 2)->nullable()->comment('目標通話成功率（%）');
            $table->decimal('target_appointment_rate', 5, 2)->nullable()->comment('目標アポ獲得率（%）');
            
            // 有効期間
            $table->date('effective_from')->comment('有効開始日');
            $table->date('effective_until')->nullable()->comment('有効終了日（NULL=無期限）');
            $table->boolean('is_active')->default(true)->comment('アクティブフラグ');
            
            $table->timestamps();
            
            // インデックス
            $table->unique(['user_id', 'is_active', 'effective_from'], 'unique_user_active_target');
            $table->index(['user_id', 'effective_from', 'effective_until'], 'idx_user_period');
            $table->index(['is_active', 'effective_from'], 'idx_active_targets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kpi_targets');
    }
};
