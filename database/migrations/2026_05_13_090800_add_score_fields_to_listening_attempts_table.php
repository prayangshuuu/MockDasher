<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listening_attempts', function (Blueprint $table) {
            if (! Schema::hasColumn('listening_attempts', 'total_correct')) {
                $table->integer('total_correct')->nullable()->after('status');
            }
            if (! Schema::hasColumn('listening_attempts', 'band_score')) {
                $table->float('band_score')->nullable()->after('total_correct');
            }
        });
    }

    public function down(): void
    {
        Schema::table('listening_attempts', function (Blueprint $table) {
            $table->dropColumn(['total_correct', 'band_score']);
        });
    }
};
