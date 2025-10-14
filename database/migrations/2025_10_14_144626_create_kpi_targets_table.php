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
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->date('target_month');
        $table->unsignedInteger('calls_target')->default(0);
        $table->unsignedInteger('appointments_target')->default(0);
        $table->unsignedInteger('deals_target')->default(0);
        $table->timestamps();

        $table->unique(['user_id', 'target_month'], 'user_month_unique');
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
