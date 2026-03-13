<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reading_question_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reading_passage_id')->constrained()->cascadeOnDelete();
            $table->string('group_instruction')->nullable(); // e.g. "Questions 1–7: Choose the correct letter, A, B or C."
            $table->string('question_type');                 // multiple_choice, tfng, ynng, matching_headings, etc.
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reading_question_groups');
    }
};
