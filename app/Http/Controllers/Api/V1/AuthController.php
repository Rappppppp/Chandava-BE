<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\StoreUserRequest;
use App\Models\User;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    // Login and return JWT token
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            // Attempt JWT authentication
            if (!$token = auth('api')->attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['message' => 'Login failed. Please try again later.'], 500);
        }
    }

    // Register user and return JWT token
    public function register(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();

            unset($validated['role']); // force role to user

            $user = User::create([
                'role' => 'user',
                ...$validated,
                'password' => Hash::make($validated['password']),
            ]);

            $token = auth('api')->login($user);

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json(['message' => 'Registration failed. Please try again later.'], 500);
        }
    }

    // Logout JWT user
    public function logout()
    {
        try {
            auth('api')->logout();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json(['message' => 'Logout failed. Please try again later.'], 500);
        }
    }

    // Get currently authenticated user
    public function me()
    {
        try {
            $user = auth('api')->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return new UserResource($user);
        } catch (Exception $e) {
            Log::error('Me fetch error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to retrieve user.'], 500);
        }
    }

    // Optional: Refresh JWT token
    public function refresh()
    {
        try {
            $token = auth('api')->refresh();
            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Token refresh error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to refresh token.'], 500);
        }
    }

    // Format token response
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => new UserResource(auth('api')->user()),
        ]);
    }
}
