<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Author;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorFeatureTest extends TestCase
{
    use RefreshDatabase;
    protected $adminUser;
    protected $librarianUser;
    protected $author;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'role' => 'Admin',
        ]);

        $this->librarianUser = User::factory()->create([
            'role' => 'Librarian',
        ]);

        $this->author = Author::factory()->create();
    }



    /** @test */
    public function admin_or_librarian_can_create_an_author()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/v1/authors', [
            'name' => 'New Author',
            'bio' => 'Author bio',
            'birthdate' => now()->toDateString(),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_or_librarian_can_update_an_author()
    {
        Sanctum::actingAs($this->librarianUser);

        $response = $this->putJson('/api/v1/authors/' . $this->author->id, [
            'name' => 'Updated Author Name',
            'bio' => 'Updated bio',
            'birthdate' => $this->author->birthdate,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_can_delete_an_author()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson('/api/v1/authors/' . $this->author->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);
    }
}
