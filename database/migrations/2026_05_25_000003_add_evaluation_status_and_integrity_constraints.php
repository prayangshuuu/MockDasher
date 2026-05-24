<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_writing_evaluations', function (Blueprint $table) {
            $table->string('evaluation_status')->default('pending')->after('band_score');
            $table->text('failure_reason')->nullable()->after('evaluation_status');
            $table->unique('test_attempt_id', 'awe_test_attempt_unique');
            $table->index(['user_id', 'evaluation_status'], 'awe_user_status_index');
        });

        Schema::table('ai_speaking_evaluations', function (Blueprint $table) {
            $table->string('evaluation_status')->default('pending')->after('band_score');
            $table->text('failure_reason')->nullable()->after('evaluation_status');
            $table->unique('test_attempt_id', 'ase_test_attempt_unique');
            $table->index(['user_id', 'evaluation_status'], 'ase_user_status_index');
        });

        Schema::table('writing_answers', function (Blueprint $table) {
            $table->unique(['test_attempt_id', 'writing_task_id'], 'wa_attempt_task_unique');
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'completed_at'], 'ta_user_status_completed_index');
            $table->index(['test_set_id', 'completed_at'], 'ta_test_set_completed_index');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropIndex('ta_user_status_completed_index');
            $table->dropIndex('ta_test_set_completed_index');
        });

        Schema::table('writing_answers', function (Blueprint $table) {
            $table->dropUnique('wa_attempt_task_unique');
        });

        Schema::table('ai_speaking_evaluations', function (Blueprint $table) {
            $table->dropIndex('ase_user_status_index');
            $table->dropUnique('ase_test_attempt_unique');
            $table->dropColumn(['evaluation_status', 'failure_reason']);
        });

        Schema::table('ai_writing_evaluations', function (Blueprint $table) {
            $table->dropIndex('awe_user_status_index');
            $table->dropUnique('awe_test_attempt_unique');
            $table->dropColumn(['evaluation_status', 'failure_reason']);
        });
    }
};
