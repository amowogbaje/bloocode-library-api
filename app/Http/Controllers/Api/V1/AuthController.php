<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }





    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');
            $response = $this->authService->login($credentials['email'], $credentials['password']);

            return response()->json([
                'access_token' => $response['access_token'],
                'message' => 'Login successful',
                'token_type' => $response['token_type'],
                'data' => $response['data'],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function logout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Throwable $e) {
            Log::error('Error during logout: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while logging out. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
