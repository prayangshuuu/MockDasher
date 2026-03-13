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
        Schema::create('writing_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('task_number'); // 1 or 2
            $table->string('task_title');
            $table->text('task_description')->nullable();
            $table->text('task_prompt')->nullable();
            $table->text('instruction_text')->nullable();
            $table->integer('minimum_word_count')->default(150);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('writing_tasks');
    }
};
