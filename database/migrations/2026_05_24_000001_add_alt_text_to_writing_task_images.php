<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add alt_text column to writing_task_images.
     * This text is what the admin enters to describe the graph/chart/table.
     * It is sent to Gemini as the IMAGE_DESCRIPTION for Task 1 evaluation.
     */
    public function up(): void
    {
        Schema::table('writing_task_images', function (Blueprint $table) {
            $table->longText('alt_text')->nullable()->after('image_path')
                ->comment('Admin description of the chart/graph sent to Gemini for AI evaluation');
        });
    }

    public function down(): void
    {
        Schema::table('writing_task_images', function (Blueprint $table) {
            $table->dropColumn('alt_text');
        });
    }
};
