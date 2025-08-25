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
        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();  // 担当者
            $table->date('target_month')->index();                           // 対象月(1日固定想定)
            $table->unsignedInteger('calls_target');                         // 架電目標
            $table->unsignedInteger('appointments_target');                  // アポ目標
            $table->unsignedInteger('deals_target');                         // 成約目標
            $table->timestamps();
            $table->unique(['user_id','target_month']);                      // 月別ユニーク
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
    }
};
