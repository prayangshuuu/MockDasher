<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listening_sections', function (Blueprint $table) {
            $table->text('instruction_text')->nullable()->after('section_number');
        });
    }

    public function down(): void
    {
        Schema::table('listening_sections', function (Blueprint $table) {
            $table->dropColumn('instruction_text');
        });
    }
};
