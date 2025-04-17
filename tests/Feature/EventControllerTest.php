<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_list_events_with_pagination()
    {
        Event::factory()->count(15)->create();

        $response = $this->getJson('/api/v1/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'event_id',
                        'event_name',
                        'status'
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
    public function can_filter_events_by_status()
    {
        Event::factory()->count(3)->create(['status' => 'active']);
        Event::factory()->count(2)->create(['status' => 'inactive']);

        $response = $this->getJson('/api/v1/events?status=active');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function can_search_events_by_name()
    {
        Event::factory()->create(['event_name' => 'Test Event 1']);
        Event::factory()->create(['event_name' => 'Another Event']);

        $response = $this->getJson('/api/v1/events?search=Test');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function can_create_event()
    {
        $response = $this->postJson('/api/v1/events', [
            'event_name' => 'Test Event',
            'description' => 'Test Description',
            'status' => 'inactive'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Event created successfully.'
            ]);

        $this->assertDatabaseHas('events', [
            'event_name' => 'Test Event'
        ]);
    }

    /** @test */
    public function can_show_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson('/api/v1/events/' . $event->event_id);

        $response->assertStatus(200)
            ->assertJson([
                'event_id' => $event->event_id,
                'event_name' => $event->event_name
            ]);
    }

    /** @test */
    public function can_update_event()
    {
        $event = Event::factory()->create();

        $response = $this->putJson('/api/v1/events/' . $event->event_id, [
            'event_name' => 'Updated Event',
            'description' => $event->description,
            'status' => $event->status
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Event updated successfully.'
            ]);

        $this->assertDatabaseHas('events', [
            'event_id' => $event->event_id,
            'event_name' => 'Updated Event'
        ]);
    }

    /** @test */
    public function can_delete_event()
    {
        $event = Event::factory()->create();

        $response = $this->deleteJson('/api/v1/events/' . $event->event_id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('events', [
            'event_id' => $event->event_id
        ]);
    }

    /** @test */
    public function can_start_inactive_event()
    {
        $event = Event::factory()->create(['status' => 'inactive']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/start');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Event started successfully.'
            ]);

        $this->assertDatabaseHas('events', [
            'event_id' => $event->event_id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function cannot_start_non_inactive_event()
    {
        $event = Event::factory()->create(['status' => 'active']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/start');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Only inactive events can be started.'
            ]);
    }

    /** @test */
    public function can_finalize_active_event()
    {
        $event = Event::factory()->create(['status' => 'active']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/finalize');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Event finalized successfully.'
            ]);

        $this->assertDatabaseHas('events', [
            'event_id' => $event->event_id,
            'status' => 'completed'
        ]);
    }

    /** @test */
    public function cannot_finalize_non_active_event()
    {
        $event = Event::factory()->create(['status' => 'inactive']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/finalize');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Only active events can be finalized.'
            ]);
    }

    /** @test */
    public function can_reset_inactive_event()
    {
        $event = Event::factory()->create(['status' => 'inactive']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/reset');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Event reset successfully.'
            ]);
    }

    /** @test */
    public function cannot_reset_non_inactive_event()
    {
        $event = Event::factory()->create(['status' => 'active']);

        $response = $this->postJson('/api/v1/events/' . $event->event_id . '/reset');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Only inactive events can be reset.'
            ]);
    }
}