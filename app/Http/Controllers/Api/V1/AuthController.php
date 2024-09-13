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
