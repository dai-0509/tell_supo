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
        // カラムを文字列型に変更（インデックスは保持）
        Schema::table('customers', function (Blueprint $table) {
            $table->string('temperature_rating', 10)->nullable()->change();
        });
        
        // 既存データを変換
        DB::table('customers')->where('temperature_rating', 'A')->update(['temperature_rating' => '高']);
        DB::table('customers')->where('temperature_rating', 'B')->update(['temperature_rating' => '高']);
        DB::table('customers')->where('temperature_rating', 'C')->update(['temperature_rating' => '中']);
        DB::table('customers')->where('temperature_rating', 'D')->update(['temperature_rating' => '中']);
        DB::table('customers')->where('temperature_rating', 'E')->update(['temperature_rating' => '低']);
        DB::table('customers')->where('temperature_rating', 'F')->update(['temperature_rating' => '低']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // データを元に戻す
        DB::table('customers')->where('temperature_rating', '高')->update(['temperature_rating' => 'A']);
        DB::table('customers')->where('temperature_rating', '中')->update(['temperature_rating' => 'C']);
        DB::table('customers')->where('temperature_rating', '低')->update(['temperature_rating' => 'E']);
        
        // カラムを元のENUM型に戻す
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('temperature_rating', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable()->change();
        });
    }
};