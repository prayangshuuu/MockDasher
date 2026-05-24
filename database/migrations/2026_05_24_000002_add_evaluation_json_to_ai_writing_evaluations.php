<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add per-task evaluation JSON columns to ai_writing_evaluations.
     * This stores the full Gemini response for each task for admin review.
     */
    public function up(): void
    {
        Schema::table('ai_writing_evaluations', function (Blueprint $table) {
            $table->longText('task_1_evaluation_json')->nullable()->after('evaluation_text');
            $table->longText('task_2_evaluation_json')->nullable()->after('task_1_evaluation_json');
            $table->decimal('task_1_band_score', 3, 1)->nullable()->after('task_2_evaluation_json');
            $table->decimal('task_2_band_score', 3, 1)->nullable()->after('task_1_band_score');
        });
    }

    public function down(): void
    {
        Schema::table('ai_writing_evaluations', function (Blueprint $table) {
            $table->dropColumn(['task_1_evaluation_json', 'task_2_evaluation_json', 'task_1_band_score', 'task_2_band_score']);
        });
    }
};
