<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_returns_profile(): void
    {
        $user = User::factory()->create(['country' => 'Bangladesh']);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJson([
                'id'      => $user->id,
                'email'   => $user->email,
                'country' => 'Bangladesh',
            ])
            ->assertJsonStructure(['id', 'name', 'email', 'country', 'has_gemini_key', 'avatar_url']);
    }

    // ── Update ─────────────────────────────────────────────────────────────────

    public function test_update_profile_succeeds(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile', [
            'first_name'        => 'Jane',
            'last_name'         => 'Doe',
            'email'             => $user->email,
            'country'           => 'Australia',
            'target_band_score' => 7.5,
            'exam_type'         => 'Academic',
            'exam_date'         => '2026-08-01',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Profile updated successfully.'])
            ->assertJsonPath('user.first_name', 'Jane')
            ->assertJsonPath('user.last_name', 'Doe')
            ->assertJsonPath('user.name', 'Jane Doe');

        $this->assertDatabaseHas('users', [
            'id'      => $user->id,
            'country' => 'Australia',
        ]);
    }

    public function test_update_profile_validates_required_fields(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson('/api/v1/profile', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email']);
    }

    public function test_update_rejects_duplicate_email(): void
    {
        $other = User::factory()->create(['email' => 'taken@example.com']);
        $user  = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile', [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'taken@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    // ── Update password ────────────────────────────────────────────────────────

    public function test_update_password_succeeds(): void
    {
        $user = User::factory()->create(['password' => bcrypt('OldPass1!')]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile/password', [
            'current_password'      => 'OldPass1!',
            'password'              => 'NewPass9@',
            'password_confirmation' => 'NewPass9@',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password updated successfully.']);

        $this->assertTrue(Hash::check('NewPass9@', $user->fresh()->password));
    }

    public function test_update_password_fails_on_wrong_current(): void
    {
        $user = User::factory()->create(['password' => bcrypt('RealPass1!')]);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile/password', [
            'current_password'      => 'WrongPass',
            'password'              => 'NewPass9@',
            'password_confirmation' => 'NewPass9@',
        ]);

        $response->assertStatus(422);
    }

    // ── Gemini key ─────────────────────────────────────────────────────────────

    public function test_update_gemini_key_saves_valid_key(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['models' => []], 200),
        ]);

        $response = $this->putJson('/api/v1/profile/gemini-key', [
            'gemini_api_key' => 'AIzaFakeKeyForTesting12345',
        ]);

        $response->assertStatus(200)
            ->assertJson(['has_gemini_key' => true]);
    }

    public function test_update_gemini_key_rejects_invalid_format(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson('/api/v1/profile/gemini-key', [
            'gemini_api_key' => 'not-a-real-key',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['gemini_api_key']);
    }

    public function test_clear_gemini_key(): void
    {
        $user = User::factory()->create(['gemini_api_key' => 'AIzaSomeKey']);
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/profile/gemini-key', [
            'gemini_api_key' => '',
        ]);

        $response->assertStatus(200);
        $this->assertNull($user->fresh()->getRawOriginal('gemini_api_key'));
    }

    // ── Delete account ─────────────────────────────────────────────────────────

    public function test_destroy_deletes_account_with_correct_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('MyPass1!')]);
        Sanctum::actingAs($user);
        $id = $user->id;

        $response = $this->deleteJson('/api/v1/profile', [
            'current_password' => 'MyPass1!',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Account deleted successfully.']);

        $this->assertDatabaseMissing('users', ['id' => $id]);
    }

    public function test_destroy_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('MyPass1!')]);
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/v1/profile', [
            'current_password' => 'WrongPass',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
