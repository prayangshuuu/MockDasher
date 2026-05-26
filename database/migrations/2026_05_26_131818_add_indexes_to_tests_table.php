<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            // ── (status, created_at) ───────────────────────────────────────────
            // Covers every published-test listing query:
            //   User::index  → ->where('status','published')->latest()->get()
            //   Api::index   → ->where('status','published')->orderByDesc('created_at')
            //   Dashboard    → ->where('status','published')->count()
            //                  (MySQL satisfies COUNT with the index alone)
            // Column order: equality predicate first, range/sort column second —
            // the standard B-tree rule for composite indexes.
            $table->index(['status', 'created_at'], 'tests_status_created_index');

            // ── (status, book_number, year) ────────────────────────────────────
            // Covers the originally reported gap and any admin/API sort by
            // catalogue position:
            //   ->where('status','published')->orderBy('book_number')->orderBy('year')
            // Having all three columns in the index lets MySQL avoid a filesort
            // and makes the index covering for the sort key.
            $table->index(['status', 'book_number', 'year'], 'tests_status_book_year_index');
        });
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropIndex('tests_status_book_year_index');
            $table->dropIndex('tests_status_created_index');
        });
    }
};
