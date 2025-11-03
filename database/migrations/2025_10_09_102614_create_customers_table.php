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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('company_name', 120);
            $table->string('contact_name', 60)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('industry', 60)->nullable();

            $table->enum('temperature_rating', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable();
            $table->string('area', 60)->nullable();

            $table->string('status', 30)->default('new');
            $table->unsignedTinyInteger('priority')->default(3);

            $table->text('memo')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'temperature_rating']);
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
