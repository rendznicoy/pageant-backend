<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;

class CandidateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_list_candidates_with_pagination()
    {
        $event = Event::factory()->create();
        Candidate::factory()->count(15)->create(['event_id' => $event->event_id]);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/candidates');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'candidate_id',
                        'first_name',
                        'last_name'
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
    public function can_filter_candidates_by_sex()
    {
        $event = Event::factory()->create();
        Candidate::factory()->count(5)->create(['event_id' => $event->event_id, 'sex' => 'male']);
        Candidate::factory()->count(3)->create(['event_id' => $event->event_id, 'sex' => 'female']);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/candidates?sex=male');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function can_search_candidates_by_name_or_team()
    {
        $event = Event::factory()->create();
        Candidate::factory()->create(['event_id' => $event->event_id, 'first_name' => 'John', 'team' => 'Team A']);
        Candidate::factory()->create(['event_id' => $event->event_id, 'last_name' => 'Doe', 'team' => 'Team B']);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/candidates?search=John');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/candidates?search=Team B');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_create_candidate_with_photo()
    {
        Storage::fake('public');
        $event = Event::factory()->create();
        $file = UploadedFile::fake()->image('candidate.jpg');

        $response = $this->postJson('/api/v1/events/{$event->event_id}/candidates/create', [
            'event_id' => $event->event_id,
            'first_name' => 'Test',
            'last_name' => 'Candidate',
            'sex' => 'male',
            'team' => 'Test Team',
            'photo' => $file,
            'candidate_number' => '1',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Candidate created successfully.'
            ]);

        $this->assertDatabaseHas('candidates', [
            'first_name' => 'Test',
            'last_name' => 'Candidate'
        ]);
    }

    /** @test */
    public function can_show_candidate()
    {
        $candidate = Candidate::factory()->create();

        $response = $this->getJson('/api/v1/events/' . $candidate->event_id . '/candidates/' . $candidate->candidate_id);

        $response->assertStatus(200)
            ->assertJson([
                'candidate_id' => $candidate->candidate_id,
                'first_name' => $candidate->first_name
            ]);
    }

    /** @test */
    public function can_update_candidate()
    {
        $candidate = Candidate::factory()->create();

        $response = $this->patchJson("/api/v1/events/{$candidate->event_id}/candidates/{$candidate->candidate_id}/edit", [
            'event_id' => $candidate->event_id,
            'candidate_id' => $candidate->candidate_id,
            'first_name' => 'Updated',
            'last_name' => $candidate->last_name,
            'sex' => $candidate->sex,
            'team' => $candidate->team
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Candidate updated successfully.'
            ]);

        $this->assertDatabaseHas('candidates', [
            'candidate_id' => $candidate->candidate_id,
            'first_name' => 'Updated'
        ]);
    }

    /** @test */
    public function can_delete_candidate()
    {
        $candidate = Candidate::factory()->create();

        $response = $this->deleteJson('/api/v1/events/' . $candidate->event_id . '/candidates/' . $candidate->candidate_id, [
            'event_id' => $candidate->event_id
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('candidates', [
            'candidate_id' => $candidate->candidate_id
        ]);
    }
}