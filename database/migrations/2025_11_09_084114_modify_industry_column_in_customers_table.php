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
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('industry', [
                'IT',
                '製造業',
                '小売業',
                '金融業',
                '医療・福祉',
                '教育',
                '建設・不動産',
                '運輸・物流',
                '飲食・宿泊',
                '士業・コンサル',
                'その他',
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('industry', 60)->nullable()->change();
        });
    }
};
