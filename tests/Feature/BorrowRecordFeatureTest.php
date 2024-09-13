<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BorrowRecord;
use App\Models\Book;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BorrowRecordFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $librarianUser;
    protected $memberUser;
    protected $book;
    protected $borrowRecord;

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

        $this->borrowRecord = BorrowRecord::factory()->create([
            'user_id' => $this->memberUser->id,
            'book_id' => $this->book->id,
            'borrowed_at' => Carbon::now()->subDays(1),
            'due_at' => Carbon::now()->addDays(14),
        ]);
    }

    /** @test */
    public function admin_or_librarian_can_retrieve_all_borrow_records()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('api/v1/borrow-records');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }

    

    /** @test */
    public function admin_or_librarian_can_retrieve_a_specific_borrow_record()
    {
        Sanctum::actingAs($this->librarianUser);

        $response = $this->getJson('api/v1/borrow-records/' . $this->borrowRecord->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }
}
