<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'username' => 'testuser',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'user_id',
                    'username',
                    'role'
                ],
                'token'
            ]);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/v1/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/v1/register', [
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Judge',
            'email' => 'newuser@gmail.com',
            'first_name' => 'New',
            'last_name' => 'User',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registration successful.'
            ])
            ->assertJsonStructure([
                'user',
                'token'
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser'
        ]);
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/v1/register', [
            'username' => '',
            'password' => 'short',
            'role' => 'invalid'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password', 'role']);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/logout');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_logout()
    {
        $response = $this->postJson('/api/v1/logout');

        dump($response->getContent()); // Debugging line
        $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthenticated.']);
    }
}