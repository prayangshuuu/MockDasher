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
        Schema::create('ai_writing_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->text('task_1_answer')->nullable();
            $table->text('task_2_answer')->nullable();
            $table->longText('evaluation_text')->nullable();
            $table->decimal('band_score', 3, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_writing_evaluations');
    }
};
