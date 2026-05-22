<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('writing_answers', function (Blueprint $table) {
            $table->longText('evaluation_json')->nullable()->after('submitted_at');
            $table->decimal('band_score', 3, 1)->nullable()->after('evaluation_json');
        });
    }

    public function down(): void
    {
        Schema::table('writing_answers', function (Blueprint $table) {
            $table->dropColumn(['evaluation_json', 'band_score']);
        });
    }
};
