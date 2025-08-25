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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');                 // 会社名
            $table->string('contact_name')->nullable();     // 担当者名
            $table->string('email')->nullable()->unique();  // メール（ユニーク）
            $table->string('phone')->nullable()->index();   // 電話（検索頻度高）
            $table->string('industry')->nullable();         // 業種
            $table->enum('status', ['new','contacted','interested','negotiating','won','lost'])
                  ->default('new')->index();                // 進捗ステータス
            $table->enum('priority', ['high','medium','low'])->default('medium');
            $table->text('memo')->nullable();               // 備考
            $table->timestamps();
            // $table->softDeletes(); // 論理削除したい場合
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
