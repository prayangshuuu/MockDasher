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
        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->foreignId('test_attempt_id')->nullable()->after('test_set_id')->constrained('test_attempts')->nullOnDelete();
        });
        Schema::table('listening_attempts', function (Blueprint $table) {
            $table->foreignId('test_attempt_id')->nullable()->after('test_set_id')->constrained('test_attempts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->dropForeign(['test_attempt_id']);
            $table->dropColumn('test_attempt_id');
        });
        Schema::table('listening_attempts', function (Blueprint $table) {
            $table->dropForeign(['test_attempt_id']);
            $table->dropColumn('test_attempt_id');
        });
    }
};
