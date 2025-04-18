<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    /** @test */
    public function can_list_all_users()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonCount(6); // 5 created + 1 from setUp
    }

    /** @test */
    public function can_create_user()
    {
        $response = $this->postJson('/api/v1/users', [
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'judge',
            'email' => 'newuser@gmail.com',
            'first_name' => 'New',
            'last_name' => 'User',
            'role' => 'tabulator'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully.'
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'role' => 'tabulator'
        ]);
    }

    /** @test */
    public function cannot_create_user_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/users', [
            'username' => '',
            'password' => 'short',
            'role' => 'invalid'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password', 'role']);
    }

    /** @test */
    public function can_show_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/user');

        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $user->user_id,
                'username' => $user->username
            ]);
    }

    /** @test */
    public function can_update_user()
    {
        $user = User::factory()->create();

        $response = $this->patchJson('/api/v1/users/' . $user->user_id, [
            'username' => 'updateduser',
            'role' => 'tabulator',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully.'
            ]);

        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'username' => 'updateduser'
        ]);
    }

    /** @test */
    public function can_update_user_password()
    {
        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $response = $this->patchJson('/api/v1/users/' . $user->user_id, [
            'username' => $user->username,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'role' => 'tabulator',
        ]);

        $response->assertStatus(200);
        $user->refresh();
        $this->assertNotEquals($oldPasswordHash, $user->password);
    }

    /** @test */
    public function can_delete_user()
    {
        $user = User::factory()->create();

        $requestData = [
            'user_id' => $user->user_id,
        ];

        $response = $this->deleteJson('/api/v1/users/' . $user->user_id, $requestData);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'user_id' => $user->user_id
        ]);
    }
}