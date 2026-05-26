<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Login ──────────────────────────────────────────────────────────────────

    public function test_login_returns_token_on_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token', 'token_type', 'expires_at',
                'user' => ['id', 'name', 'email', 'is_admin'],
            ])
            ->assertJson(['token_type' => 'Bearer']);
    }

    public function test_login_fails_on_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_fails_on_unknown_email(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'nobody@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_accepts_device_name(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'       => $user->email,
            'password'    => 'secret123',
            'device_name' => 'iPhone 15 Pro',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name'         => 'iPhone 15 Pro',
        ]);
    }

    // ── Me ─────────────────────────────────────────────────────────────────────

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson([
                'id'    => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401)
            ->assertJson(['error' => 'unauthenticated']);
    }

    // ── Logout ─────────────────────────────────────────────────────────────────

    public function test_logout_deletes_current_token_from_db(): void
    {
        $user  = User::factory()->create(['password' => bcrypt('secret123')]);
        $token = $user->createToken('TestDevice');
        $tokenId = $token->accessToken->id;

        $this->withToken($token->plainTextToken)
            ->postJson('/api/v1/auth/logout')
            ->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully.']);

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId]);
    }

    public function test_logout_all_removes_all_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('Device1');
        $user->createToken('Device2');

        $this->assertSame(2, $user->tokens()->count());

        Sanctum::actingAs($user);
        $this->postJson('/api/v1/auth/logout-all')->assertStatus(200);

        $this->assertSame(0, $user->tokens()->count());
    }

    // ── Unauthenticated error shape ────────────────────────────────────────────

    public function test_protected_routes_return_json_401(): void
    {
        foreach ([
            ['GET', '/api/v1/auth/me'],
            ['GET', '/api/v1/profile'],
            ['GET', '/api/v1/tests'],
            ['GET', '/api/v1/attempts'],
            ['GET', '/api/v1/history'],
        ] as [$method, $url]) {
            $response = $this->json($method, $url);
            $response->assertStatus(401)
                ->assertJson(['error' => 'unauthenticated']);
        }
    }
}
