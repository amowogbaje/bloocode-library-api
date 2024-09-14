<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;
use App\Http\Requests\AuthorRequest;
use App\Services\AuthorService;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorController extends Controller
{
    protected $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }


    public function index()
    {
        try {
            $authors = $this->authorService->getAllAuthors();
            return $this->success('Authors retrieved successfully', AuthorResource::collection($authors));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving authors: ' . $e->getMessage());
            return $this->error('An error occurred while retrieving authors. Please try again later.', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $author = $this->authorService->getAuthorById($id);
            return $this->success('Author retrieved successfully', new AuthorResource($author));
        } catch (ModelNotFoundException $e) {
            return $this->error('Author not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving author: ' . $e->getMessage());
            return $this->error('An error occurred while retrieving the author. Please try again later.', $e->getMessage());
        }
    }

    public function store(AuthorRequest $request)
    {
        try {
            $this->authorize('create', Author::class);
            $validatedData = $request->validated();
            $author = $this->authorService->createAuthor($validatedData);
            return $this->success('Author created successfully', new AuthorResource($author), Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->error('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (QueryException $e) {
            return $this->error('Database Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            Log::error('Error creating author: ' . $e->getMessage());
            return $this->error('An error occurred while creating the author. Please try again later.', $e->getMessage());
        }
    }


    public function update(AuthorRequest $request, $id)
    {
        try {
            $author = $this->authorService->getAuthorById($id);
            $this->authorize('update', $author);
            $validatedData = $request->validated();
            $author = $this->authorService->updateAuthor($author, $validatedData);
            return $this->success('Author updated successfully', new AuthorResource($author));
        } catch (ValidationException $e) {
            return $this->error('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return $this->error('Author not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            return $this->error('Database Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            Log::error('Error updating author: ' . $e->getMessage());
            return $this->error('An error occurred while updating the author. Please try again later.', $e->getMessage());
        }
    }



    public function destroy($id)
    {
        try {
            $author = $this->authorService->getAuthorById($id);
            $this->authorize('delete', $author);
            $this->authorService->deleteAuthor($author);
            return $this->success('Author deleted successfully', null);
        } catch (ModelNotFoundException $e) {
            return $this->error('Author not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error deleting author: ' . $e->getMessage());
            return $this->error('An error occurred while deleting the author. Please try again later.', $e->getMessage());
        }
    }
}
