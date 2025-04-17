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

class PdfReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_download_event_report()
    {
        $event = Event::factory()->create(['status' => 'completed']);
        $judge = Judge::factory()->create(['event_id' => $event->event_id]);
        $category = Category::factory()->create(['event_id' => $event->event_id]);
        $candidate = Candidate::factory()->create(['event_id' => $event->event_id]);
        Score::factory()->create([
            'event_id' => $event->event_id,
            'judge_id' => $judge->judge_id,
            'category_id' => $category->category_id,
            'candidate_id' => $candidate->candidate_id
        ]);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/report?event_id=' . $event->event_id, [
            'event_id' => $event->event_id
        ]);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function cannot_download_report_for_non_existent_event()
    {
        $response = $this->postJson('/api/v1/reports/download', [
            'event_id' => 'nonexistent'
        ]);

        $response->assertStatus(404);
    }
}