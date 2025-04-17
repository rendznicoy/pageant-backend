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
        Score::factory()->count(3)->create([
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id
        ]);

        $response = $this->getJson('/api/v1/scores?event_id=' . $this->event->event_id);

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    /** @test */
    public function can_submit_score_for_active_event()
    {
        $response = $this->postJson('/api/v1/scores', [
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $this->candidate->candidate_id,
            'category_id' => $this->category->category_id,
            'score' => 85
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Score submitted successfully.'
            ]);

        $this->assertDatabaseHas('scores', [
            'event_id' => $this->event->event_id,
            'score' => 85
        ]);
    }

    /** @test */
    public function cannot_submit_score_for_inactive_event()
    {
        $inactiveEvent = Event::factory()->create(['status' => 'inactive']);
        $category = Category::factory()->create(['event_id' => $inactiveEvent->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $inactiveEvent->event_id]);

        $response = $this->postJson('/api/v1/scores', [
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 85
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scoring is disabled. Event is inactive.'
            ]);
    }

    /** @test */
    public function cannot_submit_score_for_completed_event()
    {
        $completedEvent = Event::factory()->create(['status' => 'completed']);
        $category = Category::factory()->create(['event_id' => $completedEvent->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $completedEvent->event_id]);

        $response = $this->postJson('/api/v1/scores', [
            'event_id' => $completedEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $candidate->candidate_id,
            'category_id' => $category->category_id,
            'score' => 85
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scoring is closed. Event has already been completed.'
            ]);
    }

    /** @test */
    public function can_update_score_for_active_event()
    {
        $score = Score::factory()->create([
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $this->candidate->candidate_id,
            'category_id' => $this->category->category_id,
            'score' => 80
        ]);

        $response = $this->putJson('/api/v1/scores', [
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $this->candidate->candidate_id,
            'category_id' => $this->category->category_id,
            'score' => 90
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Score updated successfully.'
            ]);

        $this->assertDatabaseHas('scores', [
            'score_id' => $score->score_id,
            'score' => 90
        ]);
    }

    /** @test */
    public function cannot_update_score_for_non_active_event()
    {
        $inactiveEvent = Event::factory()->create(['status' => 'inactive']);
        $score = Score::factory()->create([
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'score' => 80
        ]);

        $response = $this->putJson('/api/v1/scores', [
            'event_id' => $inactiveEvent->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $this->candidate->candidate_id,
            'category_id' => $this->category->category_id,
            'score' => 90
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Scores can only be updated for active events.'
            ]);
    }

    /** @test */
    public function can_delete_score()
    {
        $score = Score::factory()->create([
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id
        ]);

        $response = $this->deleteJson('/api/v1/scores', [
            'event_id' => $this->event->event_id,
            'judge_id' => $this->judge->judge_id,
            'candidate_id' => $this->candidate->candidate_id,
            'category_id' => $this->category->category_id
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('scores', [
            'score_id' => $score->score_id
        ]);
    }
}