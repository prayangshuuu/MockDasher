<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── test_attempts ──────────────────────────────────────────────────────
        // (1) Every list/history query: ->where('user_id')->latest()->paginate()
        //     The existing (user_id, status, completed_at) index cannot satisfy
        //     ORDER BY created_at — MySQL must filesort after the filter.
        // (2) Resume-detection in start(): ->where('user_id')->where('test_set_id')
        //     ->whereNull('completed_at') — test_set_id is a post-filter range scan
        //     without this index.
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'ta_user_created_index');
            $table->index(['user_id', 'test_set_id', 'completed_at'], 'ta_user_testset_completed_index');
        });

        // ── content ordering indexes ───────────────────────────────────────────
        // Each module loads its content with ->orderBy(sort_column). Without a
        // composite index starting with test_set_id, MySQL must sort after filtering.

        // ->where('test_set_id')->orderBy('task_number')
        Schema::table('writing_tasks', function (Blueprint $table) {
            $table->index(['test_set_id', 'task_number'], 'wt_set_task_index');
        });

        // ->where('test_set_id')->orderBy('part')->orderBy('id')
        Schema::table('speaking_questions', function (Blueprint $table) {
            $table->index(['test_set_id', 'part', 'id'], 'sq_set_part_id_index');
        });

        // ->where('test_set_id')->orderBy('section_number')
        Schema::table('listening_sections', function (Blueprint $table) {
            $table->index(['test_set_id', 'section_number'], 'ls_set_section_index');
        });

        // ->where('test_set_id')->orderBy('passage_number')
        Schema::table('reading_passages', function (Blueprint $table) {
            $table->index(['test_set_id', 'passage_number'], 'rp_set_passage_index');
        });

        // ── submitted_at covering indexes ──────────────────────────────────────
        // Duplicate-submission guard: ->where([test_attempt_id, task/question_id])
        //   ->whereNotNull('submitted_at')->exists()
        // The UNIQUE index covers the row lookup; adding submitted_at lets the
        // EXISTS check resolve without a row data read.
        Schema::table('writing_answers', function (Blueprint $table) {
            $table->index(['test_attempt_id', 'submitted_at'], 'wa_attempt_submitted_index');
        });

        Schema::table('speaking_answers', function (Blueprint $table) {
            $table->index(['test_attempt_id', 'submitted_at'], 'sa_attempt_submitted_index');
        });

        // ── module attempt status indexes ──────────────────────────────────────
        // hasOne('test_attempt_id') + status check on every API request.
        // InnoDB's FK index covers the lookup; adding status avoids a row read
        // for the completed/in_progress status predicate.
        Schema::table('listening_attempts', function (Blueprint $table) {
            $table->index(['test_attempt_id', 'status'], 'la_attempt_status_index');
        });

        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->index(['test_attempt_id', 'status'], 'ra_attempt_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('reading_attempts', function (Blueprint $table) {
            $table->dropIndex('ra_attempt_status_index');
        });

        Schema::table('listening_attempts', function (Blueprint $table) {
            $table->dropIndex('la_attempt_status_index');
        });

        Schema::table('speaking_answers', function (Blueprint $table) {
            $table->dropIndex('sa_attempt_submitted_index');
        });

        Schema::table('writing_answers', function (Blueprint $table) {
            $table->dropIndex('wa_attempt_submitted_index');
        });

        Schema::table('reading_passages', function (Blueprint $table) {
            $table->dropIndex('rp_set_passage_index');
        });

        Schema::table('listening_sections', function (Blueprint $table) {
            $table->dropIndex('ls_set_section_index');
        });

        Schema::table('speaking_questions', function (Blueprint $table) {
            $table->dropIndex('sq_set_part_id_index');
        });

        Schema::table('writing_tasks', function (Blueprint $table) {
            $table->dropIndex('wt_set_task_index');
        });

        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropIndex('ta_user_testset_completed_index');
            $table->dropIndex('ta_user_created_index');
        });
    }
};
