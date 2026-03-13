<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listening_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('current_section')->default(1); // 1-4
            $table->string('status')->default('in_progress'); // in_progress, transfer, completed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('transfer_started_at')->nullable(); // when 10-min answer transfer begins
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listening_attempts');
    }
};
