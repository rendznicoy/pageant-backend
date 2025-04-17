<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_list_categories_with_pagination()
    {
        $event = Event::factory()->create();
        Category::factory()->count(15)->create(['event_id' => $event->event_id]);

        $response = $this->getJson('/api/v1/events/' . $event->event_id . '/categories/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'category_id',
                        'category_name',
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
    public function can_create_category()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/events/{$event->event_id}/categories/create', [
            'event_id' => $event->event_id,
            'category_name' => 'Talent',
            'category_weight' => '30',
            'max_score' => '10',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Category created successfully.'
            ]);

        $this->assertDatabaseHas('categories', [
            'category_name' => 'Talent',
            'event_id' => $event->event_id,
        ]);
    }

    /** @test */
    public function cannot_create_category_with_invalid_percentage()
    {
        $event = Event::factory()->create();

        $response = $this->postJson('/api/v1/categories', [
            'event_id' => $event->event_id,
            'category_name' => 'Test Category',
            'percentage' => 150 // Invalid percentage
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['percentage']);
    }

    /** @test */
    public function can_show_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/v1/categories/' . $category->category_id . '?event_id=' . $category->event_id);

        $response->assertStatus(200)
            ->assertJson([
                'category_id' => $category->category_id,
                'category_name' => $category->category_name
            ]);
    }

    /** @test */
    public function can_update_category()
    {
        $category = Category::factory()->create();

        $response = $this->putJson('/api/v1/categories/' . $category->category_id, [
            'event_id' => $category->event_id,
            'category_name' => 'Updated Category',
            'percentage' => 40
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Category updated successfully.'
            ]);

        $this->assertDatabaseHas('categories', [
            'category_id' => $category->category_id,
            'category_name' => 'Updated Category'
        ]);
    }

    /** @test */
    public function can_delete_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/v1/categories/' . $category->category_id, [
            'event_id' => $category->event_id
        ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', [
            'category_id' => $category->category_id
        ]);
    }
}