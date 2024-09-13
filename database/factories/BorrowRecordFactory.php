<?php

namespace Database\Factories;

use App\Models\BorrowRecord;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BorrowRecordFactory extends Factory
{
    protected $model = BorrowRecord::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Assuming the User factory exists
            'book_id' => Book::factory(), // Assuming the Book factory exists
            'borrowed_at' => Carbon::now()->subDays(1),
            'due_at' => Carbon::now()->addDays(14),
            'returned_at' => $this->faker->optional()->dateTime,
        ];
    }
}
