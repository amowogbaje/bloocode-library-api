<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;



use App\Http\Resources\BookResource;
use App\Http\Resources\BorrowRecordResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BorrowRecord;
use Illuminate\Auth\Access\AuthorizationException;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }


    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'search' => 'sometimes|string|max:255',
                'page' => 'sometimes|integer|min:1',
                'page_size' => 'sometimes|integer|min:1|max:100',
                'sort' => 'sometimes|in:asc,desc',
            ]);

            $books = $this->bookService->getBooks($validatedData);
            $data = [
                'count' => $books->total(),
                'next' => $books->nextPageUrl() ? $books->nextPageUrl() . '&page_size=' . ($validatedData['page_size'] ?? 10) : null,
                'previous' => $books->previousPageUrl() ? $books->previousPageUrl() . '&page_size=' . ($validatedData['page_size'] ?? 10) : null,
                'books' => BookResource::collection($books->items()),
            ];

            return $this->success('Books retrieved successfully', $data);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving books: ' . $e->getMessage());
            return $this->error('An error occurred while retrieving books. Please try again later.', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $book = $this->bookService->getBookById($id);
            return $this->success('Book retrieved successfully', new BookResource($book));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving book: ' . $e->getMessage());
            return $this->error('An error occurred while retrieving the book. Please try again later.', $e->getMessage());
        }
    }

    public function store(StoreBookRequest $request)
    {
        try {
            $this->authorize('create', Book::class);

            $validatedData = $request->validated();
            $book = $this->bookService->createBook($validatedData);
            return $this->success('Book created successfully', new BookResource($book), Response::HTTP_CREATED);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            Log::error('Error creating book: ' . $e->getMessage());
            return $this->error('An error occurred while creating the book. Please try again later.', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
            $this->authorize('update', $book);

            $request->validate([
                'title' => 'sometimes|required|string',
                'isbn' => 'sometimes|required|string|unique:books,isbn,' . $book->id,
                'published_date' => 'nullable|date',
                'author_id' => 'sometimes|required|exists:authors,id',
                'status' => 'sometimes|required|in:Available,Borrowed',
            ]);

            $book->update($request->all());
            return $this->success('Book updated successfully', new BookResource($book->refresh()));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            Log::error('Error updating book: ' . $e->getMessage());
            return $this->error('An error occurred while updating the book. Please try again later.', $e->getMessage());
        }
    }



    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
            $this->authorize('delete', $book);
            $book->delete();
            return $this->success('Book deleted successfully');
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error deleting book: ' . $e->getMessage());
            return $this->error('An error occurred while deleting the book. Please try again later.', $e->getMessage());
        }
    }

    public function borrow(Request $request, $id)
    {
        try {
            $request->validate([
                'due_date' => 'nullable|date',
                'book_id' => 'nullable|date',
            ]);
            $borrowRecord = $this->bookService->borrowBook($id, $request);
            return $this->success('Book borrowed successfully', new BorrowRecordResource($borrowRecord));
        } catch (\Throwable $e) {
            Log::error('Error borrowing book: ' . $e->getMessage());
            return $this->error('An error occurred while borrowing the book. Please try again later.', $e->getMessage());
        }
    }



    public function return($id)
    {
        try {
            $borrowRecord = $this->bookService->returnBook($id);
            return $this->success('Book returned successfully', new BorrowRecordResource($borrowRecord));
        } catch (\Throwable $e) {
            Log::error('Error returning book: ' . $e->getMessage());
            return $this->error('An error occurred while returning the book. Please try again later.', $e->getMessage());
        }
    }
}
