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
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['ielts_collection_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('ielts_collection_id')->nullable()->change();
            $table->foreign('ielts_collection_id')
                ->references('id')
                ->on('ielts_collections')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['ielts_collection_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('ielts_collection_id')->nullable(false)->change();
            $table->foreign('ielts_collection_id')
                ->references('id')
                ->on('ielts_collections')
                ->cascadeOnDelete();
        });
    }
};
