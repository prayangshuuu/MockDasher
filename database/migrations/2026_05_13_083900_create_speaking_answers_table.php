<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('speaking_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('speaking_question_id')->constrained()->cascadeOnDelete();
            $table->string('audio_path')->nullable();
            $table->longText('transcript_text')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->unique(['test_attempt_id', 'speaking_question_id'], 'sa_attempt_question_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('speaking_answers');
    }
};
