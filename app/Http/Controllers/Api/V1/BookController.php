<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Book::class);
        $books = Book::all();
        return response()->json([
            'message' => 'Books retrieved successfully',
            'data' => BookResource::collection($books)
        ], 200);
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);
        $this->authorize('view', $book);
        return response()->json([
            'message' => 'Book retrieved successfully',
            'data' => new BookResource($book)
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Book::class);

        $request->validate([
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books',
            'published_date' => 'nullable|date',
            'author_id' => 'required|exists:authors,id',
            'status' => 'required|in:Available,Borrowed',
        ]);

        $book = Book::create($request->all());
        return response()->json([
            'message' => 'Book created successfully',
            'data' => new BookResource($book)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $this->authorize('update', $book);

        $request->validate([
            'title' => 'required|string',
            'isbn' => 'required|string|unique:books,isbn,' . $book->id,
            'published_date' => 'nullable|date',
            'author_id' => 'required|exists:authors,id',
            'status' => 'required|in:Available,Borrowed',
        ]);

        $book->update($request->all());
        return response()->json([
            'message' => 'Book updated successfully',
            'data' => new BookResource($book)
        ], 200);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $this->authorize('delete', $book);
        $book->delete();
        return response()->json([
            'message' => 'Book deleted successfully'
        ], 200);
    }

    public function borrow($id)
    {
        $book = Book::findOrFail($id);
        $this->authorize('borrow', $book);

        if ($book->status === 'Available') {
            $book->update(['status' => 'Borrowed']);
            return response()->json([
                'message' => 'Book borrowed successfully',
                'data' => new BookResource($book)
            ], 200);
        }

        return response()->json([
            'message' => 'Book is not available for borrowing',
            'error' => 'Book not available'
        ], 400);
    }

    public function return($id)
    {
        $book = Book::findOrFail($id);
        $this->authorize('return', $book);

        if ($book->status === 'Borrowed') {
            $book->update(['status' => 'Available']);
            return response()->json([
                'message' => 'Book returned successfully',
                'data' => new BookResource($book)
            ], 200);
        }

        return response()->json([
            'message' => 'Book is not currently borrowed',
            'error' => 'Book not borrowed'
        ], 400);
    }
}