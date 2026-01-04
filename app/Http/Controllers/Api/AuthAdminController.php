<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthAdminController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',

        ]);

        if ($validator->fails()) {
            return response()->api(
                'Validation failed',
                ['errors' => $validator->errors()],
                422
            );
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->api(
            'User registered successfully',
            [
                'admin' => $admin,
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

    $admin = Admin::where('email', $request->email)->first();

    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return response()->api('Invalid credentials', null, 401);
    }

    $token = $admin->createToken('auth_token')->plainTextToken;

    return response()->api(
        'Login successful',
        [
            'admin' => $admin,
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
        $request->user('admin')->currentAccessToken()->delete();
;

        return response()->api(
            'Logout successful',
            null,
            200
        );
    }

    /**
     * Get authenticated user
     */
    public function admin(Request $request)
    {
        return response()->api('Admin retrieved successfully', $request->user('admin'), 200);
    }

    /**
     * Get authenticated user (alias for admin method)
     * Used by frontend UserForm.vue
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->api('Unauthenticated', null, 401);
        }

        // Load relationships if it's a User model
        if ($user instanceof \App\Models\User) {
            $user->load(['employer', 'vendor', 'roles']);
        }

        return response()->api('User retrieved successfully', $user, 200);
    }
}

