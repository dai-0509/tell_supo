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
        Schema::table('user_kpi_targets', function (Blueprint $table) {
            // 既存のdaily_call_targetを削除（後で曜日別に置き換え）
            $table->dropColumn('daily_call_target');
            
            // 曜日別の架電目標
            $table->unsignedInteger('monday_call_target')->default(0)->after('user_id')->comment('月曜日の架電目標');
            $table->unsignedInteger('tuesday_call_target')->default(0)->after('monday_call_target')->comment('火曜日の架電目標');
            $table->unsignedInteger('wednesday_call_target')->default(0)->after('tuesday_call_target')->comment('水曜日の架電目標');
            $table->unsignedInteger('thursday_call_target')->default(0)->after('wednesday_call_target')->comment('木曜日の架電目標');
            $table->unsignedInteger('friday_call_target')->default(0)->after('thursday_call_target')->comment('金曜日の架電目標');
            $table->unsignedInteger('saturday_call_target')->default(0)->after('friday_call_target')->comment('土曜日の架電目標');
            $table->unsignedInteger('sunday_call_target')->default(0)->after('saturday_call_target')->comment('日曜日の架電目標');
            
            // 過去実績から算出される推奨値
            $table->decimal('historical_success_rate', 5, 2)->nullable()->comment('過去の平均通話成功率(%)');
            $table->decimal('historical_appointment_rate', 5, 2)->nullable()->comment('過去の平均アポ獲得率(%)');
            $table->unsignedInteger('recommended_monthly_calls')->nullable()->comment('推奨月次架電数');
            $table->unsignedInteger('recommended_weekly_calls')->nullable()->comment('推奨週次架電数');
            
            // 設定方法のフラグ
            $table->enum('setting_method', ['manual', 'auto_distributed', 'ai_suggested'])->default('manual')->comment('設定方法');
            $table->json('weekday_distribution_ratio')->nullable()->comment('曜日別配分比率');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_kpi_targets', function (Blueprint $table) {
            // 曜日別フィールドを削除
            $table->dropColumn([
                'monday_call_target',
                'tuesday_call_target',
                'wednesday_call_target',
                'thursday_call_target',
                'friday_call_target',
                'saturday_call_target',
                'sunday_call_target',
                'historical_success_rate',
                'historical_appointment_rate',
                'recommended_monthly_calls',
                'recommended_weekly_calls',
                'setting_method',
                'weekday_distribution_ratio'
            ]);
            
            // 元のdaily_call_targetを復元
            $table->unsignedInteger('daily_call_target')->after('user_id')->comment('日次架電目標');
        });
    }
};
