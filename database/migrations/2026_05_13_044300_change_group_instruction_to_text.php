<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_question_groups', function (Blueprint $table) {
            $table->text('group_instruction')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('reading_question_groups', function (Blueprint $table) {
            $table->string('group_instruction')->nullable()->change();
        });
    }
};
