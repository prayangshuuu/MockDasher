<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->timestamp('writing_started_at')->nullable()->after('started_at');
            $table->timestamp('speaking_started_at')->nullable()->after('writing_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn(['writing_started_at', 'speaking_started_at']);
        });
    }
};
