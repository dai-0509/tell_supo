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
        Schema::table('scripts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('script_type')->default('opening');
            $table->integer('usage_count')->default(0);
            $table->softDeletes();
            
            // body カラムを content に変更
            $table->renameColumn('body', 'content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scripts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'script_type', 'usage_count']);
            $table->dropSoftDeletes();
            
            // content カラムを body に戻す
            $table->renameColumn('content', 'body');
        });
    }
};
