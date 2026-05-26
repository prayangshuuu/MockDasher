<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_sets', function (Blueprint $table) {
            // Covers every ordered set listing within a test:
            //   ->where('test_id', $id)->orderBy('set_number')   (admin show, user browsing)
            //   ->where('test_id', $id)->first()                  (attempt start by test_id)
            // The FK auto-index on test_id alone forces a filesort for the ORDER BY.
            $table->index(['test_id', 'set_number'], 'ts_test_set_number_index');
        });
    }

    public function down(): void
    {
        Schema::table('test_sets', function (Blueprint $table) {
            $table->dropIndex('ts_test_set_number_index');
        });
    }
};
