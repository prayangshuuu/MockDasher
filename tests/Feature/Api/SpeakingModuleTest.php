<?php

namespace Tests\Feature\Api;

use App\Jobs\EvaluateSpeakingSubmission;
use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SpeakingModuleTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeContext(): array
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
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);
        $question = SpeakingQuestion::create([
            'test_set_id'   => $testSet->id,
            'part'          => 1,
            'question_text' => 'Tell me about your hometown.',
            'time_limit'    => 60,
        ]);

        return [$user, $attempt, $testSet, $question];
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_sets_speaking_started_at_and_returns_parts(): void
    {
        [$user, $attempt] = $this->makeContext();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/speaking");

        $response->assertStatus(200)
            ->assertJsonStructure(['attempt_id', 'remaining_seconds', 'parts']);

        $this->assertNotNull($attempt->fresh()->speaking_started_at);
    }

    public function test_show_returns_403_for_other_user(): void
    {
        [$user, $attempt] = $this->makeContext();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/speaking");

        $response->assertStatus(403);
    }

    public function test_show_returns_409_if_already_evaluated(): void
    {
        [$user, $attempt] = $this->makeContext();
        AiSpeakingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => 7.0,
            'evaluation_status' => 'completed',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/speaking");

        $response->assertStatus(409)->assertJson(['error' => 'already_completed']);
    }

    // ── Upload audio ───────────────────────────────────────────────────────────

    private function fakeAudioFile(string $name = 'recording.mp3'): UploadedFile
    {
        // Minimal valid MP3 frame header so finfo detects it as audio/mpeg
        $mp3Header = "\xff\xfb\x90\x00" . str_repeat("\x00", 413);

        return UploadedFile::fake()->createWithContent($name, $mp3Header);
    }

    public function test_upload_audio_stores_file(): void
    {
        Storage::fake('public');
        [$user, $attempt, , $question] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/speaking/upload", [
            'question_id' => $question->id,
            'audio'       => $this->fakeAudioFile(),
            'transcript'  => 'I love my hometown.',
            'duration'    => 30,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('speaking_answers', [
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
        ]);
    }

    public function test_upload_audio_rejects_foreign_question(): void
    {
        Storage::fake('public');
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);

        $otherSet = TestSet::create(['test_id' => $attempt->testSet->test_id, 'set_number' => 2]);
        $foreignQ = SpeakingQuestion::create([
            'test_set_id'   => $otherSet->id,
            'part'          => 1,
            'question_text' => 'Foreign question.',
            'time_limit'    => 60,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/speaking/upload", [
            'question_id' => $foreignQ->id,
            'audio'       => $this->fakeAudioFile(),
        ]);

        $response->assertStatus(403);
    }

    public function test_upload_audio_validates_file_is_required(): void
    {
        [$user, $attempt, , $question] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/speaking/upload", [
            'question_id' => $question->id,
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['audio']);
    }

    // ── Submit question ────────────────────────────────────────────────────────

    public function test_submit_question_marks_submitted_at(): void
    {
        [$user, $attempt, , $question] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
        SpeakingAnswer::create([
            'user_id'              => $user->id,
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
            'transcript_text'      => 'My answer.',
            'duration_seconds'     => 30,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/attempts/{$attempt->id}/speaking/questions/{$question->id}/submit"
        );

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertNotNull(
            SpeakingAnswer::where([
                'test_attempt_id'      => $attempt->id,
                'speaking_question_id' => $question->id,
            ])->first()?->submitted_at
        );
    }

    public function test_submit_question_returns_409_on_duplicate(): void
    {
        [$user, $attempt, , $question] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
        SpeakingAnswer::create([
            'user_id'              => $user->id,
            'test_attempt_id'      => $attempt->id,
            'speaking_question_id' => $question->id,
            'transcript_text'      => 'My answer.',
            'duration_seconds'     => 30,
            'submitted_at'         => now(),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/attempts/{$attempt->id}/speaking/questions/{$question->id}/submit"
        );

        $response->assertStatus(409);
    }

    // ── Submit (full module) ───────────────────────────────────────────────────

    public function test_submit_dispatches_evaluation_job(): void
    {
        Queue::fake();
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['speaking_started_at' => now(), 'status' => 'speaking']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/speaking/submit");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'evaluation_status' => 'pending']);

        Queue::assertPushed(EvaluateSpeakingSubmission::class);

        $this->assertDatabaseHas('ai_speaking_evaluations', [
            'test_attempt_id'   => $attempt->id,
            'evaluation_status' => 'pending',
        ]);
    }

    public function test_submit_returns_403_on_completed_attempt(): void
    {
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/speaking/submit");

        $response->assertStatus(403);
    }

    // ── Result ─────────────────────────────────────────────────────────────────

    public function test_result_returns_evaluation_data(): void
    {
        [$user, $attempt] = $this->makeContext();
        AiSpeakingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => 6.0,
            'evaluation_status' => 'completed',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/speaking/result");

        $response->assertStatus(200)
            ->assertJsonPath('evaluation_status', 'completed');
        $this->assertEquals(6.0, $response->json('speaking_band'));
    }

    public function test_result_returns_403_for_other_user(): void
    {
        [$user, $attempt] = $this->makeContext();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/speaking/result");

        $response->assertStatus(403);
    }
}
