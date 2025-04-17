<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Category;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Score;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $judgeUser;
    protected $event;
    protected $judge;
    protected $category;
    protected $candidate;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->judgeUser = User::factory()->create(['role' => 'judge']);
        $this->actingAs($this->judgeUser);
        
        $this->event = Event::factory()->create(['status' => 'active']);
        $this->judge = Judge::factory()->create([
            'event_id' => $this->event->event_id,
            'user_id' => $this->judgeUser->user_id
        ]);
        $this->category = Category::factory()->create(['event_id' => $this->event->event_id]);
        $this->candidate = Candidate::factory()->create(['event_id' => $this->event->event_id]);
    }

    /** @test */
    public function can_list_scores()
    {
        $user = User::factory()->create(['role' => 'tabulator']); // or 'Tabulator'
        $this->actingAs($user);

        Score::factory()->count(3)->create([
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id,
        ]);

        $response = $this->getJson('/api/v1/events/' . $this->event->event_id . '/scores?event_id=' . $this->event->event_id);

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function can_submit_score_for_active_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $event = Event::factory()->create(['status' => 'active']);
        
        // Debug: Check if the event was actually created with the right ID
        $this->assertDatabaseHas('events', ['event_id' => $event->event_id]);
        
        $judge = Judge::factory()->create(['event_id' => $event->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $event->event_id]);
        $category = Category::factory()->create(['event_id' => $event->event_id]);

        $response = $this->postJson("/api/v1/events/{$event->event_id}/scores/create", [
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 8,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Score submitted successfully.'
            ]);

        $this->assertDatabaseHas('scores', [
            'event_id' => $event->event_id,
            'score' => 8
        ]);
    }

    /** @test */
    public function cannot_submit_score_for_inactive_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $inactiveEvent = Event::factory()->create(['status' => 'inactive']);
        $category = Category::factory()->create(['event_id' => $inactiveEvent->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $inactiveEvent->event_id]);

        $response = $this->postJson("/api/v1/events/{$inactiveEvent->event_id}/scores/create", [
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 10
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scoring is disabled. Event is inactive.'
            ]);
    }

    /** @test */
    public function cannot_submit_score_for_completed_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $completedEvent = Event::factory()->create(['status' => 'completed']);
        $category = Category::factory()->create(['event_id' => $completedEvent->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $completedEvent->event_id]);

        $response = $this->postJson("/api/v1/events/{$completedEvent->event_id}/scores/create", [
            'event_id' => $completedEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 10
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scoring is closed. Event has already been completed.'
            ]);
    }

    /** @test */
    public function can_update_score_for_active_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Create fresh test data within this test
        $event = Event::factory()->create(['status' => 'active']);
        $judge = Judge::factory()->create(['event_id' => $event->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $event->event_id]);
        $category = Category::factory()->create(['event_id' => $event->event_id]);

        // First create a score using the newly created instances
        $score = Score::create([
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 8
        ]);

        // Then update it using the newly created instances
       $response = $this->patchJson("/api/v1/events/{$event->event_id}/scores/edit/{$judge->judge_id}/{$candidate->candidate_id}/{$category->category_id}", [
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 5
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Score updated successfully.'
            ]);

        $this->assertDatabaseHas('scores', [
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 5
        ]);
    }

    /** @test */
    public function cannot_update_score_for_non_active_event()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Create fresh test data within this test
        $inactiveEvent = Event::factory()->create(['status' => 'inactive']);
        $judge = Judge::factory()->create(['event_id' => $inactiveEvent->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $inactiveEvent->event_id]);
        $category = Category::factory()->create(['event_id' => $inactiveEvent->event_id]);

        $score = Score::factory()->create([
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $judge->judge_id,
            'score' => 10
        ]);

        $response = $this->patchJson("/api/v1/events/{$inactiveEvent->event_id}/scores/edit/{$judge->judge_id}/{$candidate->candidate_id}/{$category->category_id}", [
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 5
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scores can only be updated for active events.'
            ]);
    }

    /** @test */
    public function can_delete_score()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        // Create fresh test data within this test
        $event = Event::factory()->create(['status' => 'inactive']);
        $judge = Judge::factory()->create(['event_id' => $event->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $event->event_id]);
        $category = Category::factory()->create(['event_id' => $event->event_id]);

        $score = Score::factory()->create([
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id, // Ensure these are set
            'category_id' => $category->category_id,   // when creating the score
        ]);

        $requestData = [
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id
        ];

        $response = $this->deleteJson("/api/v1/events/{$event->event_id}/scores/delete", $requestData);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('scores', [
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
        ]);
    }
}