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
        Schema::table('kpi_targets', function (Blueprint $table) {
            // 既存のカラムを削除
            $table->dropUnique(['user_id','target_month']);
            $table->dropIndex(['target_month']);
            $table->dropColumn(['target_month', 'calls_target', 'appointments_target', 'deals_target']);
        });
        
        Schema::table('kpi_targets', function (Blueprint $table) {
            // 新しいカラムを追加
            $table->enum('target_type', ['weekly', 'monthly'])->after('user_id');
            $table->date('target_date')->after('target_type');
            $table->unsignedInteger('call_target')->after('target_date');
            $table->unsignedInteger('appointment_target')->after('call_target');
            $table->softDeletes();
            
            // 新しいインデックス
            $table->index(['user_id', 'target_type', 'target_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpi_targets', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'target_type', 'target_date']);
            $table->dropSoftDeletes();
            $table->dropColumn(['target_type', 'target_date', 'call_target', 'appointment_target']);
            
            // 元のカラムを復元
            $table->date('target_month')->index();
            $table->unsignedInteger('calls_target');
            $table->unsignedInteger('appointments_target');
            $table->unsignedInteger('deals_target');
            $table->unique(['user_id','target_month']);
        });
    }
};
