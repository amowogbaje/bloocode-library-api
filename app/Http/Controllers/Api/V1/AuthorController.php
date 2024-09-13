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


    /**
     * @OA\Get(
     *     path="/api/v1/authors",
     *     summary="Get all authors",
     *     tags={"Authors"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Authors retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Authors retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="bio", type="string", example="An experienced author."),
     *                     @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *                 )
     *             )
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
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving authors. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/authors/{id}",
     *     summary="Get a specific author",
     *     tags={"Authors"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the author to retrieve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="An experienced author."),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author not found"),
     *             @OA\Property(property="error", type="string")
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
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving the author. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/authors",
     *     summary="Create a new author",
     *     tags={"Authors"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="An experienced author."),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Author created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="An experienced author."),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
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
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while creating the author. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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


    /**
     * @OA\Put(
     *     path="/api/v1/authors/{id}",
     *     summary="Update an existing author",
     *     tags={"Authors"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the author to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="An updated bio."),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="bio", type="string", example="An updated bio."),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="1980-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
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
     *         response=404,
     *         description="Author not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author not found"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while updating the author. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    
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

    /**
     * @OA\Delete(
     *     path="/api/v1/authors/{id}",
     *     summary="Delete an author",
     *     tags={"Authors"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the author to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Author deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author deleted successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Author not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Author not found"),
     *             @OA\Property(property="error", type="string")
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
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while deleting the author. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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
