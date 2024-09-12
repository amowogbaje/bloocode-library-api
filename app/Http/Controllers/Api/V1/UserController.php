<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;


use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @SWG\Get(
     *     path="/api/v1/users",
     *     summary="Get all users",
     *     tags={"Users"},
     *     @SWG\Response(
     *         response=200,
     *         description="Users retrieved successfully",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @SWG\Property(property="data", type="array", @SWG\Items(ref="#/components/schemas/User"))
     *         )
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Unauthorized"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="An error occurred while retrieving users. Please try again later."),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', User::class);

            $users = $this->userService->getAllUsers();

            return $this->success('Users retrieved successfully', UserResource::collection($users));

        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving users: ' . $e->getMessage());

            return $this->error('An error occurred while retrieving users. Please try again later.', $e->getMessage());
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Get a single user",
     *     tags={"Users"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User retrieved successfully"),
     *             @SWG\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="User not found",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User not found"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Unauthorized"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="An error occurred while retrieving the user. Please try again later."),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);
            $this->authorize('view', $user);

            return $this->success('User retrieved successfully', new UserResource($user));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('User not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());

            return $this->error('An error occurred while retrieving the user. Please try again later.', $e->getMessage());
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/v1/users",
     *     summary="Create a new user",
     *     tags={"Users"},
     *     @SWG\RequestBody(
     *         required=true,
     *         @SWG\JsonContent(
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(property="password", type="string"),
     *             @SWG\Property(property="role", type="string", enum={"Admin", "Librarian", "Member"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User registered successfully"),
     *             @SWG\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Validation Error"),
     *             @SWG\Property(property="errors", type="object")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="An error occurred"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $validatedData = $request->validated();

            $user = $this->userService->createUser($validatedData);

            return $this->success('User registered successfully', new UserResource($user), Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return $this->error('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            return $this->error('Database Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            return $this->error('An error occurred', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @SWG\Put(
     *     path="/api/v1/users/{id}",
     *     summary="Update a user",
     *     tags={"Users"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\RequestBody(
     *         required=false,
     *         @SWG\JsonContent(
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(property="password", type="string"),
     *             @SWG\Property(property="role", type="string", enum={"Admin", "Librarian", "Member"})
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User updated successfully"),
     *             @SWG\Property(property="data", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Validation Error"),
     *             @SWG\Property(property="errors", type="object")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="User not found",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User not found"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Authorization Error"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="An error occurred"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();

            $user = $this->userService->updateUser($id, $validatedData);

            return $this->success('User updated successfully', new UserResource($user));
        } catch (ValidationException $e) {
            return $this->error('Validation Error', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (AuthorizationException $e) {
            return $this->error('Authorization Error', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return $this->error('User not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (QueryException $e) {
            return $this->error('Database Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Throwable $e) {
            return $this->error('An error occurred', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @SWG\Delete(
     *     path="/api/v1/users/{id}",
     *     summary="Delete a user",
     *     tags={"Users"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="User not found",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="User not found"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="Unauthorized"),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     ),
     *     @SWG\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @SWG\JsonContent(
     *             @SWG\Property(property="message", type="string", example="An error occurred while deleting the user. Please try again later."),
     *             @SWG\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);

            return $this->success('User deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('User not found', $e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error deleting user: ' . $e->getMessage());

            return $this->error('An error occurred while deleting the user. Please try again later.', $e->getMessage());
        }
    }
}
