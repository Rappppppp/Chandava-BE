<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!Auth::attempt($credentials, $request->boolean('remember'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $request->session()->regenerate();

            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource(Auth::user())
            ]);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());

            return response()->json(['message' => 'Login failed. Please try again later.'], 500);
        }
    }

    public function register(StoreUserRequest $request)
    {
        try {
            $validated = $request->validated();

            unset($validated['role']);

            $user = User::create([
               
                'role' => 'user',
                 ...$validated,
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'message' => 'Registration successful',
                'user' => new UserResource($user)
            ], 201);
        } catch (Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());

            return response()->json(['message' => 'Registration failed. Please try again later.'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Logged out']);
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());

            return response()->json(['message' => 'Logout failed. Please try again later.'], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return new UserResource($user);
        } catch (Exception $e) {
            Log::error('Me fetch error: ' . $e->getMessage());

            return response()->json(['message' => 'Failed to retrieve user.'], 500);
        }
    }
}
