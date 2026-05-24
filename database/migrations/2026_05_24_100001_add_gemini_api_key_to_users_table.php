<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add gemini_api_key column to users table.
     * Each user provides their own Gemini API key for AI evaluation.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gemini_api_key', 200)->nullable()->after('email')
                ->comment('User-provided Gemini API key for AI evaluation of Speaking and Writing');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gemini_api_key');
        });
    }
};
