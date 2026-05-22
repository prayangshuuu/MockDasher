<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('speaking_answers', function (Blueprint $table) {
            $table->longText('evaluation_json')->nullable()->after('duration_seconds');
            $table->decimal('band_score', 3, 1)->nullable()->after('evaluation_json');
            $table->timestamp('submitted_at')->nullable()->after('band_score');
        });
    }

    public function down(): void
    {
        Schema::table('speaking_answers', function (Blueprint $table) {
            $table->dropColumn(['evaluation_json', 'band_score', 'submitted_at']);
        });
    }
};
