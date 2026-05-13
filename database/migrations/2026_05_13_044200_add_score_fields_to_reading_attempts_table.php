<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->unsignedTinyInteger('total_correct')->nullable()->after('status');
            $table->decimal('band_score', 3, 1)->nullable()->after('total_correct');
        });
    }

    public function down(): void
    {
        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->dropColumn(['total_correct', 'band_score']);
        });
    }
};
