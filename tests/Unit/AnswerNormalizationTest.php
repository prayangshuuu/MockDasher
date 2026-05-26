<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests the answer normalization / isCorrect logic used identically in
 * ListeningApiController and ReadingApiController.
 *
 * Extracted to a unit test so it can run without a database.
 */
class AnswerNormalizationTest extends TestCase
{
    // ── normalization helper (mirrors the controller) ──────────────────────────

    private function isCorrect(?string $userAnswer, ?string $correctAnswer): bool
    {
        if ($userAnswer === null || $correctAnswer === null) {
            return false;
        }

        $normalize = function (string $s): string {
            $s = strtolower(strip_tags($s));
            $s = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s) ?? $s;

            return trim(preg_replace('/\s+/u', ' ', $s) ?? $s);
        };

        $userNorm     = $normalize($userAnswer);
        $validAnswers = array_map($normalize, explode('|', $correctAnswer));

        return in_array($userNorm, $validAnswers, true);
    }

    // ── Exact match ────────────────────────────────────────────────────────────

    public function test_exact_match_is_correct(): void
    {
        $this->assertTrue($this->isCorrect('London', 'London'));
    }

    // ── Case insensitivity ─────────────────────────────────────────────────────

    public function test_case_insensitive_match(): void
    {
        $this->assertTrue($this->isCorrect('LONDON', 'London'));
        $this->assertTrue($this->isCorrect('london', 'London'));
        $this->assertTrue($this->isCorrect('LonDon', 'London'));
    }

    // ── Leading / trailing whitespace ──────────────────────────────────────────

    public function test_leading_trailing_whitespace_is_stripped(): void
    {
        $this->assertTrue($this->isCorrect('  London  ', 'London'));
        $this->assertTrue($this->isCorrect('London', '  London  '));
    }

    // ── Internal multiple spaces ───────────────────────────────────────────────

    public function test_multiple_internal_spaces_are_collapsed(): void
    {
        $this->assertTrue($this->isCorrect('city   centre', 'city centre'));
    }

    // ── Punctuation stripping ──────────────────────────────────────────────────

    public function test_punctuation_is_stripped(): void
    {
        $this->assertTrue($this->isCorrect('City Centre!', 'city centre'));
        $this->assertTrue($this->isCorrect('city-centre', 'city centre'));
        $this->assertTrue($this->isCorrect('city,centre', 'city centre'));
    }

    // ── Pipe-delimited alternatives ────────────────────────────────────────────

    public function test_first_alternative_matches(): void
    {
        $this->assertTrue($this->isCorrect('city centre', 'city centre|city center'));
    }

    public function test_second_alternative_matches(): void
    {
        $this->assertTrue($this->isCorrect('city center', 'city centre|city center'));
    }

    public function test_multiple_alternatives_any_can_match(): void
    {
        $this->assertTrue($this->isCorrect('United Kingdom', 'UK|United Kingdom|Britain'));
        $this->assertTrue($this->isCorrect('uk', 'UK|United Kingdom|Britain'));
    }

    // ── Wrong answers ──────────────────────────────────────────────────────────

    public function test_wrong_answer_is_not_correct(): void
    {
        $this->assertFalse($this->isCorrect('Paris', 'London'));
    }

    public function test_partial_match_is_not_correct(): void
    {
        $this->assertFalse($this->isCorrect('Lon', 'London'));
        $this->assertFalse($this->isCorrect('London Bridge', 'London'));
    }

    // ── Null handling ──────────────────────────────────────────────────────────

    public function test_null_user_answer_is_not_correct(): void
    {
        $this->assertFalse($this->isCorrect(null, 'London'));
    }

    public function test_null_correct_answer_is_not_correct(): void
    {
        $this->assertFalse($this->isCorrect('London', null));
    }

    public function test_both_null_is_not_correct(): void
    {
        $this->assertFalse($this->isCorrect(null, null));
    }

    // ── HTML stripping ─────────────────────────────────────────────────────────

    public function test_html_tags_in_user_answer_are_stripped(): void
    {
        $this->assertTrue($this->isCorrect('<b>London</b>', 'London'));
    }

    // ── Unicode ────────────────────────────────────────────────────────────────

    public function test_unicode_letters_are_preserved(): void
    {
        $this->assertTrue($this->isCorrect('Zoë', 'Zoë'));
        $this->assertFalse($this->isCorrect('Zoe', 'Zoë'));
    }
}
