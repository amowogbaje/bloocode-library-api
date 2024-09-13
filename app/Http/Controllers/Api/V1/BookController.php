<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;



use App\Http\Resources\BookResource;
use App\Http\Resources\BorrowRecordResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BorrowRecord;
use Illuminate\Auth\Access\AuthorizationException;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/books",
     *     summary="Retrieve a list of books",
     *     description="Fetches a paginated list of books with optional search, sorting, and pagination.",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term for filtering books by title, ISBN, or author name.",
     *         required=false,
     *         schema={
     *             "type": "string",
     *             "example": "Harry Potter"
     *         }
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination.",
     *         required=false,
     *         schema={
     *             "type": "integer",
     *             "example": 1
     *         }
     *     ),
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="Number of items per page for pagination.",
     *         required=false,
     *         schema={
     *             "type": "integer",
     *             "example": 10
     *         }
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order of the books.",
     *         required=false,
     *         schema={
     *             "type": "string",
     *             "enum": {"asc", "desc"},
     *             "example": "desc"
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with paginated book list.",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="count",
     *                 type="integer",
     *                 example=50,
     *                 description="Total number of books."
     *             ),
     *             @OA\Property(
     *                 property="next",
     *                 type="string",
     *                 example="http://example.com/api/books?page=2&page_size=10",
     *                 description="URL for the next page of results, if available."
     *             ),
     *             @OA\Property(
     *                 property="previous",
     *                 type="string",
     *                 example="http://example.com/api/books?page=1&page_size=10",
     *                 description="URL for the previous page of results, if available."
     *             ),
     *             @OA\Property(
     *                 property="books",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Book Title"),
     *                     @OA\Property(property="isbn", type="string", example="978-3-16-148410-0"),
     *                     @OA\Property(property="published_date", type="string", format="date", example="2023-01-01"),
     *                     @OA\Property(property="author_id", type="integer", example=1),
     *                     @OA\Property(property="status", type="string", example="available"),
     *                     @OA\Property(
     *                         property="author",
     *                         type="object",
     *                         @OA\Property(property="name", type="string", example="Author Name"),
     *                         @OA\Property(property="bio", type="string", example="Author bio."),
     *                         @OA\Property(property="birthdate", type="string", format="date", example="1970-01-01")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string", example="User does not have the necessary permissions.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving books. Please try again later."),
     *             @OA\Property(property="error", type="string", example="Detailed error message.")
     *         )
     *     ),
     *     security={
     *         {
     *             "bearerAuth": {}
     *         }
     *     }
     * )
     */

    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'search' => 'sometimes|string|max:255',
                'page' => 'sometimes|integer|min:1',
                'page_size' => 'sometimes|integer|min:1|max:100',
                'sort' => 'sometimes|in:asc,desc',
            ]);

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

            $books = $query->paginate($pageSize, ['*'], 'page', $page);
            $nextPageUrl = $books->nextPageUrl();
            $previousPageUrl = $books->previousPageUrl();
            if ($nextPageUrl) {
                $nextPageUrl .= '&page_size=' . $pageSize;
            }

            if ($previousPageUrl) {
                $previousPageUrl .= '&page_size=' . $pageSize;
            }

            $data = [
                'count' => $books->total(),
                'next' => $nextPageUrl,
                'previous' => $previousPageUrl,
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

    /**
     * @OA\Get(
     *     path="/api/v1/books/{id}",
     *     summary="Get a book by ID",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Book retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="John Doe"),
     *                 @OA\Property(property="isbn", type="string", example="66767754tt4556"),
     *                 @OA\Property(property="author_id", type="integer", example=21),
     *                 @OA\Property(property="status", type="string", example="Available"),
     *                 @OA\Property(property="published_date", type="string", format="date", example="1980-01-01"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred while retrieving the book",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving the book. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $book = Book::findOrFail($id);
            return $this->success('Book retrieved successfully', new BookResource($book));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving book: ' . $e->getMessage());
            return $this->error('An error occurred while retrieving the book. Please try again later.', $e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/books",
     *     summary="Create a new book",
     *     tags={"Books"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Sample Book Title"),
     *             @OA\Property(property="isbn", type="string", example="123-4567890123"),
     *             @OA\Property(property="published_date", type="string", format="date", example="2024-09-12"),
     *             @OA\Property(property="author_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="Available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Book created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="John Doe"),
     *                 @OA\Property(property="isbn", type="string", example="66767754tt4556"),
     *                 @OA\Property(property="author_id", type="integer", example=21),
     *                 @OA\Property(property="status", type="string", example="Available"),
     *                 @OA\Property(property="published_date", type="string", format="date", example="1980-01-01"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred while creating the book",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while creating the book. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        try {
            $this->authorize('create', Book::class);

            $request->validate([
                'title' => 'required|string',
                'isbn' => 'required|string|unique:books',
                'published_date' => 'nullable|date',
                'author_id' => 'required|exists:authors,id',
                'status' => 'required|in:Available,Borrowed',
            ]);

            $book = Book::create($request->all());
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

    /**
     * @OA\Put(
     *     path="/api/v1/books/{id}",
     *     summary="Update a book by ID",
     *     tags={"Books"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string", example="Updated Book Title"),
     *             @OA\Property(property="isbn", type="string", example="123-4567890123"),
     *             @OA\Property(property="published_date", type="string", format="date", example="2024-09-12"),
     *             @OA\Property(property="author_id", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="Available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Book updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="John Doe"),
     *                 @OA\Property(property="isbn", type="string", example="66767754tt4556"),
     *                 @OA\Property(property="author_id", type="integer", example=21),
     *                 @OA\Property(property="status", type="string", example="Available"),
     *                 @OA\Property(property="published_date", type="string", format="date", example="1980-01-01"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred while updating the book",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while updating the book. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
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
            return $this->success('Book updated successfully', new BookResource($book));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            Log::error('Error updating book: ' . $e->getMessage());
            return $this->error('An error occurred while updating the book. Please try again later.', $e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/books/{id}",
     *     summary="Delete a book by ID",
     *     tags={"Books"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Book deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred while deleting the book",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while deleting the book. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/api/v1/books/{id}/borrow",
     *     summary="Borrow a book",
     *     description="Allows a member to borrow a book if it is available.",
     *     operationId="borrowBook",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book to borrow",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"due_at"},
     *             @OA\Property(
     *                 property="due_at",
     *                 type="integer",
     *                 description="Number of days from now when the book is due for return",
     *                 example=14
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book borrowed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Book borrowed successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="book_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="borrowed_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-26T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="returned_at",
     *                     type="string",
     *                     format="date-time",
     *                     example=null
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Book not available for borrowing",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Book is not available for borrowing"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="Book not available"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Resource not found"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="No query results for model [App\\Models\\Book] 1"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The due at must be between 0 and 30."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while borrowing the book. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */
    public function borrow(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'due_at' => 'required|integer|between:0,30'
            ]);

            $book = Book::findOrFail($id);
            $this->authorize('borrow', $book);

            if ($book->status === 'Available') {
                $book->update(['status' => 'Borrowed']);

                // Create a borrow record
                $borrowRecord = BorrowRecord::create([
                    'user_id' => auth()->id(), // Current authenticated user
                    'book_id' => $book->id,
                    'borrowed_at' => now(),
                    'due_at' => now()->addDays($validatedData['due_at']), // Set due date based on validated data
                ]);
                $borrowRecord->load('book');

                return $this->success('Book borrowed successfully', new BorrowRecordResource($borrowRecord));
            }

            return $this->error('Book is not available for borrowing', 'Book not available', Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return $this->error('Resource not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ValidationException $e) {
            return $this->error('Validation error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            Log::error('Database query error: ' . $e->getMessage());
            return $this->error('An error occurred while processing your request. Please try again later.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            Log::error('Error borrowing book: ' . $e->getMessage());
            return $this->error('An error occurred while borrowing the book. Please try again later.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/v1/books/{id}/return",
     *     summary="Return a book",
     *     description="Allows a member to return a borrowed book.",
     *     operationId="returnBook",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the book to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Book returned successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="book_id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="borrowed_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-26T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="returned_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-20T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2024-09-12T10:00:00Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Book not currently borrowed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Book is not currently borrowed"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="Book not borrowed"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Resource not found"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="No query results for model [App\\Models\\Book] 1"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Validation error"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The due at must be between 0 and 30."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="An error occurred while returning the book. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */

    public function return($id)
    {
        try {
            $book = Book::findOrFail($id);
            $this->authorize('return', $book);

            if ($book->status === 'Borrowed') {
                $book->update(['status' => 'Available']);

                // Find and update the borrow record
                $borrowRecord = BorrowRecord::where('book_id', $book->id)
                    ->whereNull('returned_at') // Ensure it's not already returned
                    ->where('user_id', auth()->id()) // Ensure the record belongs to the current user
                    ->firstOrFail();

                $borrowRecord->update(['returned_at' => now()]);
                $borrowRecord->load('book'); // Load the book relationship

                return $this->success('Book returned successfully', new BorrowRecordResource($borrowRecord));
            }

            return $this->error('Book is not currently borrowed', 'Book not borrowed', Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return $this->error('Resource not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ValidationException $e) {
            return $this->error('Validation error', $e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            Log::error('Database query error: ' . $e->getMessage());
            return $this->error('An error occurred while processing your request. Please try again later.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            Log::error('Error returning book: ' . $e->getMessage());
            return $this->error('An error occurred while returning the book. Please try again later.', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
