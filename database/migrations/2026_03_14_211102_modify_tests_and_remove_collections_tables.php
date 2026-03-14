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
        // 1. Drop constraints and columns from tests
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['ielts_collection_id']);
            $table->dropColumn(['ielts_collection_id', 'title', 'number']);
            $table->integer('book_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('exam_type')->nullable(); // Academic or General
        });

        // 2. Drop ielts_collections table
        Schema::dropIfExists('ielts_collections');

        // 3. Create test_sets table
        Schema::create('test_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->integer('set_number');
            $table->timestamps();
        });

        // 4. Update module tables
        $tables = [
            'writing_tasks', 'speaking_questions', 'listening_sections', 
            'reading_passages', 'test_attempts', 'listening_attempts', 
            'reading_attempts', 'sections'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'test_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['test_id']);
                    $table->dropColumn('test_id');
                    $table->foreignId('test_set_id')->nullable()->constrained()->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not easily reversible.
    }
};
