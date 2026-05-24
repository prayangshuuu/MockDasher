<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use App\Services\GeminiContentCreatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiContentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdminUser(): User
    {
        $user = User::factory()->create();
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $user->roles()->attach($adminRole);

        return $user;
    }

    protected function createNormalUser(): User
    {
        $user = User::factory()->create();
        $userRole = Role::firstOrCreate(['name' => 'User']);
        $user->roles()->attach($userRole);

        return $user;
    }

    /**
     * Test that guests are redirected to login.
     */
    public function test_guests_cannot_access_ai_generator(): void
    {
        $response = $this->post(route('admin.ai.generate'), [
            'module_type' => 'Writing Task 1',
            'topic' => 'Climate Change',
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Test that normal users are forbidden.
     */
    public function test_normal_users_cannot_access_ai_generator(): void
    {
        $user = $this->createNormalUser();

        $response = $this->actingAs($user)->postJson(route('admin.ai.generate'), [
            'module_type' => 'Writing Task 1',
            'topic' => 'Climate Change',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test validation fails on missing fields.
     */
    public function test_admin_validation_fails_on_empty_fields(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postJson(route('admin.ai.generate'), [
            'module_type' => '',
            'topic' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['module_type', 'topic']);
    }

    /**
     * Test validation fails on invalid module type.
     */
    public function test_admin_validation_fails_on_invalid_module_type(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postJson(route('admin.ai.generate'), [
            'module_type' => 'Speaking Part 4',
            'topic' => 'Space Exploration',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['module_type']);
    }

    /**
     * Test successful AI generation.
     */
    public function test_admin_can_generate_content_successfully(): void
    {
        $admin = $this->createAdminUser();

        $mockData = [
            'db_module_type' => 'Writing Task 1',
            'db_topic_theme' => 'Solar Energy',
            'db_precontext_instructions' => 'Analyze the energy generation data chart.',
            'db_generated_questions_or_prompt' => 'The chart shows solar energy outputs...',
            'db_image_description_data' => 'A line graph plotting solar output in gigawatts.',
            'db_ready_to_save' => true,
        ];

        $mockService = $this->mock(GeminiContentCreatorService::class, function ($mock) use ($mockData) {
            $mock->shouldReceive('generate')
                ->once()
                ->with('Writing Task 1', 'Solar Energy')
                ->andReturn([
                    'success' => true,
                    'data' => $mockData,
                    'error' => null,
                ]);
        });

        $response = $this->actingAs($admin)->postJson(route('admin.ai.generate'), [
            'module_type' => 'Writing Task 1',
            'topic' => 'Solar Energy',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => $mockData,
            ]);
    }

    /**
     * Test dynamic service failure handling.
     */
    public function test_handles_service_failures_gracefully(): void
    {
        $admin = $this->createAdminUser();

        $mockService = $this->mock(GeminiContentCreatorService::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn([
                    'success' => false,
                    'data' => null,
                    'error' => 'Gemini API limit exceeded.',
                ]);
        });

        $response = $this->actingAs($admin)->postJson(route('admin.ai.generate'), [
            'module_type' => 'Writing Task 2',
            'topic' => 'Artificial Intelligence',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'Gemini API limit exceeded.',
            ]);
    }
}
