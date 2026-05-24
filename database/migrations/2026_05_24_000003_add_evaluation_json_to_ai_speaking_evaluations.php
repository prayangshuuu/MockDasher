<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add evaluation_json to ai_speaking_evaluations.
     * Stores the combined Gemini evaluation for all parts.
     */
    public function up(): void
    {
        Schema::table('ai_speaking_evaluations', function (Blueprint $table) {
            $table->longText('evaluation_json')->nullable()->after('evaluation_text');
        });
    }

    public function down(): void
    {
        Schema::table('ai_speaking_evaluations', function (Blueprint $table) {
            $table->dropColumn('evaluation_json');
        });
    }
};
