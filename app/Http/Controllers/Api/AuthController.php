<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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
            'phone' => 'nullable|string|max:20',
            'organization_id' => 'nullable|exists:vendors,id',
            'locale' => 'nullable|in:en,ar',
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
            'phone' => $request->phone,
            'organization_id' => $request->organization_id,
            'locale' => $request->locale ?? 'en',
            'role' => 'employee', // Default role
            'is_active' => true,
        ]);

        // Assign default role using Spatie
        $user->assignRole('employee');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->api(
            'User registered successfully',
            [
                'user' => new UserResource($user->load('organization', 'roles')),
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

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->api(
                'Invalid credentials',
                null,
                401
            );
        }

        if (!$user->is_active) {
            return response()->api(
                'Your account is inactive. Please contact support.',
                null,
                403
            );
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->api(
            'Login successful',
            [
                'user' => new UserResource($user->load('organization', 'roles')),
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
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('organization', 'roles', 'permissions');

        return response()->api(
            'Profile retrieved successfully',
            [
                'user' => new UserResource($user),
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            200
        );
    }

    /**
     * Get authenticated user (alias for profile)
     */
    public function user(Request $request)
    {
        return $this->profile($request);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->api(
                'Validation failed',
                ['errors' => $validator->errors()],
                422
            );
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->api(
                'Password reset link sent to your email',
                null,
                200
            );
        }

        return response()->api(
            'Unable to send reset link',
            null,
            500
        );
    }
}

