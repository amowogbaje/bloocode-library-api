<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::factory()->create([
            'role' => 'Admin',
            'password' => Hash::make('password')
        ]);

        // Create a normal user
        $this->normalUser = User::factory()->create([
            'role' => 'Member',
            'password' => Hash::make('password')
        ]);
    }

    /** @test */
    public function admin_can_retrieve_all_users()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_can_retrieve_a_specific_user()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/v1/users/' . $this->normalUser->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/v1/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'role' => 'Member',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_can_update_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->putJson('/api/v1/users/' . $this->normalUser->id, [
            'name' => 'Updated Name',
            'email' => $this->normalUser->email,
            'role' => 'Member',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_can_delete_a_user()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson('/api/v1/users/' . $this->normalUser->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => $this->normalUser->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'access_token']);
    }
}
