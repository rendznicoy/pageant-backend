<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JudgeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_list_judges_with_pagination()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'judge']);
        Judge::factory()->count(15)->create(['event_id' => $event->event_id, 'user_id' => $user->user_id]);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/judges/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'judge_id',
                        'event_id',
                        'user' => [
                            'user_id',
                            'username'
                        ]
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ]
            ]);
    }

    /** @test */
    public function can_create_judge()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'judge']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/judges/create', [
            'event_id' => $event->event_id,
            'user_id' => $user->user_id
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Judge created successfully.'
            ]);

        $this->assertDatabaseHas('judges', [
            'event_id' => $event->event_id,
            'user_id' => $user->user_id
        ]);
    }

    /** @test */
    public function cannot_create_judge_with_non_judge_user()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/judges/create', [
            'event_id' => $event->event_id,
            'user_id' => $user->user_id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function can_show_judge()
    {
        $judge = Judge::factory()->create();

        $response = $this->getJson('/api/v1/events/' . $judge->event_id . '/judges/' . $judge->judge_id);

        $response->assertStatus(200)
            ->assertJson([
                'judge_id' => $judge->judge_id,
                'event_id' => $judge->event_id
            ]);
    }

    /** @test */
    public function can_update_judge()
    {
        $judge = Judge::factory()->create();
        $newUser = User::factory()->create(['role' => 'judge']);

        $response = $this->patchJson('/api/v1/events/' . $judge->event_id . '/judges/' . $judge->judge_id . '/edit', [
            'event_id' => $judge->event_id,
            'user_id' => $newUser->user_id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Judge updated successfully.'
            ]);

        $this->assertDatabaseHas('judges', [
            'judge_id' => $judge->judge_id,
            'user_id' => $newUser->user_id
        ]);
    }

    /** @test */
    public function can_delete_judge()
    {
        $judge = Judge::factory()->create();

        $response = $this->deleteJson('/api/v1/events/' . $judge->event_id . '/judges/' . $judge->judge_id, [
            'event_id' => $judge->event_id
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('judges', [
            'judge_id' => $judge->judge_id
        ]);
    }
}