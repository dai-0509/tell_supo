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
        // 既存データを一時的にクリア
        DB::table('customers')->update(['temperature_rating' => null]);
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('temperature_rating', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('temperature_rating', ['高', '中', '低'])->nullable()->change();
        });
    }
};
