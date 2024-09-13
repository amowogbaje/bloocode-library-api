<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

class BookFeatureTest extends TestCase
{
    use RefreshDatabase;
    protected $adminUser;
    protected $librarianUser;
    protected $memberUser;
    protected $book;
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

        $this->memberUser = User::factory()->create([
            'role' => 'Member',
        ]);

        $this->book = Book::factory()->create();
        $this->author = Author::factory()->create();
    }

    /** @test */
    public function can_retrieve_all_books()
    {
        $response = $this->getJson('api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function can_retrieve_a_specific_book()
    {
        $response = $this->getJson('api/v1/books/' . $this->book->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_or_librarian_can_create_a_book()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('api/v1/books', [
            'title' => 'New Book',
            'isbn' => '1234567890',
            'published_date' => now()->toDateString(),
            'author_id' => $this->author->id,
            'status' => 'Available',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['message', 'data']);
    }

    /** @test */
    public function admin_or_librarian_can_update_a_book()
    {
        Sanctum::actingAs($this->librarianUser);

        $response = $this->putJson('api/v1/books/' . $this->book->id, [
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
    public function member_can_borrow_a_book_if_available()
    {
        Sanctum::actingAs($this->memberUser);

        $this->book->update(['status' => 'Available']);
        $response = $this->postJson('api/v1/books/' . $this->book->id . '/borrow', [
            "due_at" => 14
        ]);

        $this->book->refresh();
        $this->assertEquals('Borrowed', $this->book->status);

        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->memberUser->id,
            'book_id' => $this->book->id,
            'returned_at' => null,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'book_id',
                    'borrowed_at',
                    'due_at',
                    'returned_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /** @test */
    public function member_can_return_a_borrowed_book()
    {
        Sanctum::actingAs($this->memberUser);
        $this->book->update(['status' => 'Available']);

        $this->postJson('api/v1/books/' . $this->book->id . '/borrow', [
            "due_at" => 12
        ]);
        
        $response = $this->postJson('api/v1/books/' . $this->book->id . '/return');

        $this->book->refresh();
        $this->assertEquals('Available', $this->book->status);

        $this->assertDatabaseHas('borrow_records', [
            'user_id' => $this->memberUser->id,
            'book_id' => $this->book->id,
            'returned_at' => now(),
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'user_id',
                    'book_id',
                    'borrowed_at',
                    'due_at',
                    'returned_at',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }




    /** @test */
    public function admin_can_delete_a_book()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->deleteJson('api/v1/books/' . $this->book->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['message']);
    }
}
