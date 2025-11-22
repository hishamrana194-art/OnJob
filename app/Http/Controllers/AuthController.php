<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->api(
                'Validation failed',
                ['errors' => $validator->errors()],
                422
            );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);
        $user->assignRole($request->role);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->api(
            'User registered successfully',
            [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            201
        );
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->api(
                'Validation failed',
                ['errors' => $validator->errors()],
                422
            );
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->api(
                'Invalid credentials',
                null,
                401
            );
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->api(
            'Login successful',
            [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            200
        );
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->api(
            'Logout successful',
            null,
            200
        );
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->api(
            'User retrieved successfully',
            $request->user(),
            200
        );
    }
}

