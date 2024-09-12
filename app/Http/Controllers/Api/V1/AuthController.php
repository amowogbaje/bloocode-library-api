<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
   

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(
                [
                    'access_token' => $token, 
                    "message" => "Login Successful",
                    'token_type' => 'Bearer',
                    'data' => $user,
                ], 200);

        }  catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred. Please try again later.',
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
