<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Library API",
 *     version="1.0.0",
 *     description="Library backend API documentation",
 *     @OA\Contact(
 *         email="amowogbajegideon@gmail.com"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum"
 * )
 */

 class SwaggerController extends Controller
 {
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", example="your-access-token"),
     *             @OA\Property(property="message", type="string", example="Login Successful"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="role", type="string", example="User")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect."),
     *             @OA\Property(property="errors", type="object", additionalProperties={"type": "string"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred. Please try again later.")
     *         )
     *     )
     * )
     */
    public function POSTapiv1login() {}

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
    
    public function GETapiv1author() {}

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
    
    public function GETapiv1authorShow() {}

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
    
    public function POSTapiv1author() {}


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

    
    
    public function PUTapiv1author() {}

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
    
    public function DELETEapiv1author() {}


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

    
    public function GETapiv1Book() {}

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

    
    public function GETapiv1BookShow() {}

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

    
    public function POSTapiv1Book() {}

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
    
    public function PUTapiv1Book() {}

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
    public function DELETEapiv1Book() {}


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
    
    public function POSTapiv1BorrowBook() {}

    



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
    public function PUTapiv1ReturnBorrowedBook() {}


    /**
     * @OA\Get(
     *     path="/api/v1/borrow-records",
     *     summary="Retrieve all borrow records",
     *     description="Retrieve a list of all borrow records.",
     *     operationId="getBorrowRecords",
     *     tags={"Borrow Records"},
     *     @OA\Response(
     *         response=200,
     *         description="Borrow records retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow records retrieved successfully"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="book_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="book",
     *                         type="object",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="title",
     *                             type="string",
     *                             example="Sample Book Title"
     *                         ),
     *                         @OA\Property(
     *                             property="isbn",
     *                             type="string",
     *                             example="978-3-16-148410-0"
     *                         ),
     *                         @OA\Property(
     *                             property="published_date",
     *                             type="string",
     *                             format="date",
     *                             example="2023-01-01"
     *                         ),
     *                         @OA\Property(
     *                             property="author_id",
     *                             type="integer",
     *                             example=1
     *                         ),
     *                         @OA\Property(
     *                             property="status",
     *                             type="string",
     *                             example="available"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             format="date-time",
     *                             example="2023-01-01T00:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             format="date-time",
     *                             example="2023-01-01T00:00:00.000000Z"
     *                         )
     *                     )
     *                 )
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
     *                 property="error",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
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
     *                 example="An error occurred while retrieving borrow records. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */

    
    public function GETapiv1BorrowRecord() {}

    /**
     * @OA\Get(
     *     path="/api/v1/borrow-records/{id}",
     *     summary="Retrieve a borrow record",
     *     description="Retrieve a specific borrow record by ID.",
     *     operationId="getBorrowRecordById",
     *     tags={"Borrow Records"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the borrow record",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Borrow record retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow record retrieved successfully"
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
     *                     property="book",
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="title",
     *                         type="string",
     *                         example="Sample Book Title"
     *                     ),
     *                     @OA\Property(
     *                         property="isbn",
     *                         type="string",
     *                         example="978-3-16-148410-0"
     *                     ),
     *                     @OA\Property(
     *                         property="published_date",
     *                         type="string",
     *                         format="date",
     *                         example="2023-01-01"
     *                     ),
     *                     @OA\Property(
     *                         property="author_id",
     *                         type="integer",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="status",
     *                         type="string",
     *                         example="available"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         format="date-time",
     *                         example="2023-01-01T00:00:00.000000Z"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="borrowed_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="due_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-15T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="returned_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-10T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     format="date-time",
     *                     example="2023-01-01T00:00:00.000000Z"
     *                 )
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
     *                 property="error",
     *                 type="string",
     *                 example="You do not have permission to perform this action"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Borrow record not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Borrow record not found"
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No query results for model [App\\Models\\BorrowRecord] 1"
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
     *                 example="An error occurred while retrieving the borrow record. Please try again later."
     *             ),
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Internal server error"
     *             )
     *         )
     *     )
     * )
     */

    
    public function GETapiv1BorrowRecordShow() {}


    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string", example="Gideon"),
     *                     @OA\Property(property="email", type="string", example="amowogabssje@gmail.com"),
     *                     @OA\Property(property="role", type="string", example="Admin")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving users. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    
    public function GETapiv1users() {}

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get a single user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="name", type="string", example="Gideon"),
     *                 @OA\Property(property="email", type="string", example="amowogabje@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="Admin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving the user. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    
    public function GETapiv1usersShow() {}

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", enum={"Admin", "Librarian", "Member"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="name", type="string", example="Gideon"),
     *                 @OA\Property(property="email", type="string", example="amowogabje@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="Admin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    
    public function POSTapiv1users() {}

    /**
     * @OA\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *             @OA\Property(property="role", type="string", enum={"Admin", "Librarian", "Member"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *            @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=15),
     *                 @OA\Property(property="name", type="string", example="Gideon"),
     *                 @OA\Property(property="email", type="string", example="amowogabje@gmail.com"),
     *                 @OA\Property(property="role", type="string", example="Admin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    
    public function PUTapiv1users() {}
    
    /**
     * @OA\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred while deleting the user. Please try again later."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    
    public function DELETEapiv1users() {}


 }