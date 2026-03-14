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
        Schema::table('reading_answers', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('answer_text');
        });

        Schema::table('listening_answers', function (Blueprint $table) {
            $table->boolean('is_flagged')->default(false)->after('answer_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers_tables', function (Blueprint $table) {
            //
        });
    }
};
