<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class BookService
{
    public function getBooks($validatedData)
    {
        $page = $validatedData['page'] ?? 1;
        $pageSize = $validatedData['page_size'] ?? 10;
        $sort = $validatedData['sort'] ?? 'desc';
        $query = Book::orderBy('created_at', $sort)
            ->select('id', 'title', 'isbn', 'published_date', 'author_id', 'status')
            ->with(['author:name,bio,birthdate']);

        if (isset($validatedData['search'])) {
            $searchQuery = $validatedData['search'];
            $query->where(function ($subQuery) use ($searchQuery) {
                $subQuery->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('isbn', 'like', "%{$searchQuery}%")
                    ->orWhereHas('author', function ($authorQuery) use ($searchQuery) {
                        $authorQuery->where('name', 'like', "%{$searchQuery}%");
                    });
            });
        }

        return $query->paginate($pageSize, ['*'], 'page', $page);
    }

    public function getBookById($id)
    {
        return Book::findOrFail($id)->load('author');
    }

    public function createBook($data)
    {
        return Book::create($data);
    }

    public function updateBook($id, $data)
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }

    public function borrowBook($id, $validatedData)
    {
        $book = Book::findOrFail($id);
        if ($book->status === 'Available') {
            $book->update(['status' => 'Borrowed']);
            return BorrowRecord::create([
                'user_id' => auth()->id(),
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_at' => now()->addDays($validatedData['due_at']),
            ]);
        }
        throw new \Exception('Book is not available for borrowing');
    }

    public function returnBook($id)
    {
        $book = Book::findOrFail($id);
        if ($book->status === 'Borrowed') {
            $book->update(['status' => 'Available']);
            $borrowRecord = BorrowRecord::where('book_id', $book->id)
                ->whereNull('returned_at')
                ->where('user_id', auth()->id())
                ->firstOrFail();
            $borrowRecord->update(['returned_at' => now()]);
            return $borrowRecord;
        }
        throw new \Exception('Book is not currently borrowed');
    }
}
