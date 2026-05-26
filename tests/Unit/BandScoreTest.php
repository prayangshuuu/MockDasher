<?php

namespace Tests\Unit;

use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BandScoreTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function attemptWithScores(float $r, float $l, float $w, float $s): TestAttempt
    {
        $user = User::factory()->create();
        $test = Test::create([
            'book_number' => 1,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'completed',
            'started_at'  => now()->subHour(),
            'completed_at' => now(),
        ]);

        ReadingAttempt::create([
            'test_attempt_id' => $attempt->id,
            'user_id'         => $user->id,
            'test_set_id'     => $testSet->id,
            'status'          => 'completed',
            'band_score'      => $r,
            'started_at'      => now()->subHour(),
            'completed_at'    => now(),
        ]);

        ListeningAttempt::create([
            'test_attempt_id' => $attempt->id,
            'user_id'         => $user->id,
            'test_set_id'     => $testSet->id,
            'status'          => 'completed',
            'band_score'      => $l,
            'started_at'      => now()->subHour(),
            'completed_at'    => now(),
        ]);

        AiWritingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => $w,
            'evaluation_status' => 'completed',
        ]);

        AiSpeakingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => $s,
            'evaluation_status' => 'completed',
        ]);

        return $attempt->fresh(['readingAttempt', 'listeningAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);
    }

    // ── Overall band ───────────────────────────────────────────────────────────

    public function test_overall_band_is_null_when_fewer_than_4_modules_complete(): void
    {
        $user    = User::factory()->create();
        $test    = Test::create(['book_number' => 1, 'year' => 2026, 'exam_type' => 'Academic', 'status' => 'published']);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);

        $this->assertNull($attempt->overall_band);
    }

    public function test_overall_band_rounds_to_nearest_half(): void
    {
        // Average = (6.0 + 7.0 + 6.5 + 6.5) / 4 = 6.5 → stays 6.5
        $attempt = $this->attemptWithScores(6.0, 7.0, 6.5, 6.5);
        $this->assertSame(6.5, $attempt->overall_band);
    }

    public function test_overall_band_rounds_up_on_0_25_boundary(): void
    {
        // Average = (6.0 + 6.0 + 6.5 + 6.0) / 4 = 6.125 → rounds to 6.0
        $attempt = $this->attemptWithScores(6.0, 6.0, 6.5, 6.0);
        $this->assertSame(6.0, $attempt->overall_band);
    }

    public function test_overall_band_rounds_correctly_for_0_375_average(): void
    {
        // Average = (6.0 + 6.0 + 6.0 + 6.5) / 4 = 6.125 → 6.0
        $attempt = $this->attemptWithScores(6.0, 6.0, 6.0, 6.5);
        $this->assertSame(6.0, $attempt->overall_band);
    }

    public function test_overall_band_all_zeros_returns_zero(): void
    {
        $attempt = $this->attemptWithScores(0.0, 0.0, 0.0, 0.0);
        $this->assertSame(0.0, $attempt->overall_band);
    }

    public function test_overall_band_all_9s_returns_9(): void
    {
        $attempt = $this->attemptWithScores(9.0, 9.0, 9.0, 9.0);
        $this->assertSame(9.0, $attempt->overall_band);
    }

    public function test_overall_band_rounds_7_1_to_7(): void
    {
        // (7.0 + 7.0 + 7.5 + 7.0) / 4 = 7.125 → 7.0
        $attempt = $this->attemptWithScores(7.0, 7.0, 7.5, 7.0);
        $this->assertSame(7.0, $attempt->overall_band);
    }

    public function test_overall_band_rounds_7_375_to_7_5(): void
    {
        // (7.0 + 7.0 + 8.0 + 7.5) / 4 = 7.375 → 7.5
        $attempt = $this->attemptWithScores(7.0, 7.0, 8.0, 7.5);
        $this->assertSame(7.5, $attempt->overall_band);
    }

    // ── Individual module bands ────────────────────────────────────────────────

    public function test_reading_band_is_null_before_completion(): void
    {
        $user    = User::factory()->create();
        $test    = Test::create(['book_number' => 1, 'year' => 2026, 'exam_type' => 'Academic', 'status' => 'published']);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);
        ReadingAttempt::create([
            'test_attempt_id' => $attempt->id,
            'user_id'         => $user->id,
            'test_set_id'     => $testSet->id,
            'status'          => 'in_progress',
            'started_at'      => now(),
        ]);

        $this->assertNull($attempt->fresh('readingAttempt')->reading_band);
    }

    public function test_time_spent_formats_correctly(): void
    {
        $user    = User::factory()->create();
        $test    = Test::create(['book_number' => 1, 'year' => 2026, 'exam_type' => 'Academic', 'status' => 'published']);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'      => $user->id,
            'test_set_id'  => $testSet->id,
            'status'       => 'completed',
            'started_at'   => now()->subMinutes(90),
            'completed_at' => now(),
        ]);

        $this->assertSame('1h 30m', $attempt->time_spent);
    }

    public function test_time_spent_is_null_when_not_completed(): void
    {
        $user    = User::factory()->create();
        $test    = Test::create(['book_number' => 1, 'year' => 2026, 'exam_type' => 'Academic', 'status' => 'published']);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now()->subHour(),
        ]);

        $this->assertNull($attempt->time_spent);
    }
}
