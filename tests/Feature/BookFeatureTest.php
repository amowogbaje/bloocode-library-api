<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookFeatureTest extends TestCase
{
    use RefreshDatabase;
    protected $adminUser;
    protected $librarianUser;
    protected $memberUser;
    // protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::factory()->create([
            'role' => 'Admin',
        ]);

        // Create a librarian user
        $this->librarianUser = User::factory()->create([
            'role' => 'Librarian',
        ]);

        // Create a member user
        $this->memberUser = User::factory()->create([
            'role' => 'Member',
        ]);

        // Create a book
        $this->book = Book::factory()->create();
    }

    /** @test */
    public function can_retrieve_all_books()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function can_retrieve_a_specific_book()
    {
        $response = $this->getJson('/api/books/' . $this->book->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_or_librarian_can_create_a_book()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/books', [
            'title' => 'New Book',
            'isbn' => '1234567890',
            'published_date' => now()->toDateString(),
            'author_id' => 1,
            'status' => 'Available',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_or_librarian_can_update_a_book()
    {
        Sanctum::actingAs($this->librarianUser);

        $response = $this->putJson('/api/books/' . $this->book->id, [
            'title' => 'Updated Book Title',
            'isbn' => $this->book->isbn,
            'published_date' => $this->book->published_date,
            'author_id' => $this->book->author_id,
            'status' => 'Available',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_can_delete_a_book()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson('/api/books/' . $this->book->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);
    }

    /** @test */
    public function member_can_borrow_a_book_if_available()
    {
        Sanctum::actingAs($this->memberUser);

        $response = $this->postJson('/api/books/' . $this->book->id . '/borrow');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);
    }

    /** @test */
    public function member_can_return_a_borrowed_book()
    {
        Sanctum::actingAs($this->memberUser);

        // First, borrow the book
        $this->postJson('/api/books/' . $this->book->id . '/borrow');

        // Then, return the book
        $response = $this->postJson('/api/books/' . $this->book->id . '/return');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message']);
    }
}
