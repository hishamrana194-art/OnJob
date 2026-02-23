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
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


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
                'user' => new UserResource($user->load( 'roles')),
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
        $user = $request->user()->load( 'roles', 'permissions');

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
    // Accept both 'email' and 'identifier' field names
    $email = $request->input('email') ?? $request->input('identifier');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->api('Valid email is required', null, 422);
    }

    if (!\App\Models\User::where('email', $email)->exists()) {
        return response()->api('Email not found', null, 404);
    }

    $otp = rand(100000, 999999);

    DB::table('password_resets')->updateOrInsert(
        ['email' => $email],
        [
            'token' => $otp,
            'created_at' => now()
        ]
    );

    Mail::raw("Your OTP is: $otp. It is valid for 10 minutes.", function ($message) use ($email) {
        $message->to($email)
                ->subject('Password Reset OTP');
    });

    return response()->api('OTP sent to your email', null, 200);
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp'   => 'required|numeric'
    ]);

    $record = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->otp)
                ->first();

    if (!$record) {
        return response()->api('OTP invalid', null, 400);
    }

    $created = \Carbon\Carbon::parse($record->created_at);
    if (\Carbon\Carbon::now()->diffInMinutes($created) > 10) {
        return response()->api('OTP expired', null, 400);
    }

    return response()->api('OTP verified successfully', null, 200);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|numeric',
        'password' => 'required|min:6|confirmed',
    ]);

    $record = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->otp)
                ->first();

    if (!$record) {
        return response()->api('OTP invalid', null, 400);
    }

    $created = \Carbon\Carbon::parse($record->created_at);
    if (\Carbon\Carbon::now()->diffInMinutes($created) > 10) {
        return response()->api('OTP expired', null, 400);
    }

    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->api('Password reset successfully', null, 200);
}




    public function changePassword(Request $request){
        $user=auth()->user();
        if (!$user || !Hash::check($request->oldpassword, $user->password)) {
            return response()->api(
                'Invalid credentials',
                null,
                401
            );
        }

        $user->update([
            'password' => bcrypt($request->newPassword)
        ]);

    }



}

