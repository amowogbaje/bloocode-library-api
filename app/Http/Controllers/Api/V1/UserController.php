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
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function index(Request $request)
    {
        try {

            $users = $this->userService->getAllUsers();

            return $this->success('Users retrieved successfully', UserResource::collection($users));
        } catch (AuthorizationException $e) {
            return $this->error('Unauthorized', $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            Log::error('Error retrieving users: ' . $e->getMessage());

            return $this->error('An error occurred while retrieving users. Please try again later.', $e->getMessage());
        }
    }


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
