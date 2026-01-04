<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\Paginatable;
use App\Models\Employer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use Paginatable;

    /**
     * Display a listing of users with filters and pagination.
     */
    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);
        $authUser = auth()->user();

        $query = User::with(['employer', 'vendor', 'roles']);

        // ==================
        // Tenant Scoping
        // ==================
        $isAdmin = $authUser instanceof \App\Models\Admin;
        $isUser = $authUser instanceof \App\Models\User;

        if ($isUser && $authUser->user_type === 'employer_user') {
            // Employer users can only see users in their organization
            $query->where('employer_id', $authUser->employer_id);
        } elseif ($isUser && $authUser->user_type === 'vendor_user') {
            // Vendor users can only see users in their organization
            $query->where('vendor_id', $authUser->vendor_id);
        }
        // Admin or super admin - no scoping (sees all users)

        // ==================
        // Filters
        // ==================

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // User type filter (only for super admin or Admin)
        $isSuperAdmin = $isAdmin || !$authUser || ($isUser && (!$authUser->user_type || $authUser->user_type === 'super_admin'));
        if ($request->filled('user_type') && $isSuperAdmin) {
            $query->where('user_type', $request->user_type);
        }

        // Employer filter (only for super admin)
        if ($request->filled('employer_id') && $isSuperAdmin) {
            $query->where('employer_id', $request->employer_id);
        }

        // Vendor filter (only for super admin)
        if ($request->filled('vendor_id') && $isSuperAdmin) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $users = $query->latest()->paginate($params['per_page']);

        return response()->api(
            'Users retrieved successfully',
            $this->paginationResponse($users, UserResource::class),
            200
        );
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $authUser = auth()->user();
        $data['password'] = Hash::make($data['password']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // ==================
        // Tenant Auto-Assignment & Seat Checking
        // ==================
        $subscription = null;
        $isUser = $authUser instanceof \App\Models\User;

        if ($isUser && $authUser->user_type === 'employer_user') {
            // Employer admin creating user - auto-set employer_id
            $data['employer_id'] = $authUser->employer_id;
            $data['user_type'] = 'employer_user';
            $data['vendor_id'] = null;

            // Check subscription seats
            $employer = Employer::findOrFail($authUser->employer_id);
            $subscription = $employer->activeSubscription;

            if (!$subscription) {
                return response()->api(
                    'No active subscription found. Please subscribe to a package first.',
                    ['error' => 'subscription_required'],
                    403
                );
            }

            if (!$subscription->hasAvailableSeats()) {
                return response()->api(
                    'You have reached your seat limit. Please upgrade your package to add more users.',
                    [
                        'error' => 'seats_limit_reached',
                        'seats_limit' => $subscription->seats_limit,
                        'seats_used' => $subscription->seats_used,
                        'remaining_seats' => $subscription->remaining_seats,
                        'package_name' => $subscription->package?->getTranslation('name', 'en'),
                        'usage_percentage' => $subscription->seat_usage_percentage,
                    ],
                    403
                );
            }
        } elseif ($isUser && $authUser->user_type === 'vendor_user') {
            // Vendor admin creating user - auto-set vendor_id
            $data['vendor_id'] = $authUser->vendor_id;
            $data['user_type'] = 'vendor_user';
            $data['employer_id'] = null;

            // Check subscription seats
            $vendor = Vendor::findOrFail($authUser->vendor_id);
            $subscription = $vendor->activeSubscription;

            if (!$subscription) {
                return response()->api(
                    'No active subscription found. Please subscribe to a package first.',
                    ['error' => 'subscription_required'],
                    403
                );
            }

            if (!$subscription->hasAvailableSeats()) {
                return response()->api(
                    'You have reached your seat limit. Please upgrade your package to add more users.',
                    [
                        'error' => 'seats_limit_reached',
                        'seats_limit' => $subscription->seats_limit,
                        'seats_used' => $subscription->seats_used,
                        'remaining_seats' => $subscription->remaining_seats,
                        'package_name' => $subscription->package?->getTranslation('name', 'en'),
                        'usage_percentage' => $subscription->seat_usage_percentage,
                    ],
                    403
                );
            }
        } else {
            // Admin, Super admin or unauthenticated - use provided values
            if ($data['user_type'] === 'employer_user') {
                $data['vendor_id'] = null;
            } elseif ($data['user_type'] === 'vendor_user') {
                $data['employer_id'] = null;
            } else {
                $data['employer_id'] = null;
                $data['vendor_id'] = null;
            }
        }

        $role = $data['role'];
        unset($data['role']);

        $user = User::create($data);
        $user->assignRole($role);

        // Increment seats counter (Observer will handle this, but as backup)
        if ($subscription) {
            $subscription->incrementSeats();
        }

        return response()->api(
            'User created successfully',
            new UserResource($user->load(['employer', 'vendor', 'roles'])),
            201
        );
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $targetUser = User::with(['employer', 'vendor', 'roles', 'permissions'])->findOrFail($id);
        $authUser = auth()->user();

        // Authorization check
        if (!$this->canAccessUser($authUser, $targetUser)) {
            return response()->api('Unauthorized - This user does not belong to your organization', null, 403);
        }

        return response()->api(
            'User retrieved successfully',
            new UserResource($targetUser),
            200
        );
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $targetUser = User::findOrFail($id);
        $authUser = auth()->user();

        // Authorization check
        if (!$this->canAccessUser($authUser, $targetUser)) {
            return response()->api('Unauthorized - This user does not belong to your organization', null, 403);
        }

        $data = $request->validated();

        // Handle password
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($targetUser->avatar) {
                Storage::disk('public')->delete($targetUser->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Handle role change
        if (isset($data['role'])) {
            $targetUser->syncRoles([$data['role']]);
            unset($data['role']);
        }

        $targetUser->update($data);

        return response()->api(
            'User updated successfully',
            new UserResource($targetUser->load(['employer', 'vendor', 'roles'])),
            200
        );
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, string $id)
    {
        $targetUser = User::findOrFail($id);
        $authUser = auth()->user();

        // Prevent self-deletion
        if ($authUser && $authUser->id == $targetUser->id) {
            return response()->api('You cannot delete your own account', null, 403);
        }

        // Authorization check
        if (!$this->canAccessUser($authUser, $targetUser)) {
            return response()->api('Unauthorized - This user does not belong to your organization', null, 403);
        }

        // Delete avatar if exists
        if ($targetUser->avatar) {
            Storage::disk('public')->delete($targetUser->avatar);
        }

        $targetUser->delete();

        return response()->api('User deleted successfully', null, 200);
    }

    /**
     * Check if auth user can access target user
     */
    protected function canAccessUser($authUser, $targetUser): bool
    {
        // If no auth user, deny access
        if (!$authUser) {
            return false;
        }

        // Admin model from admins table = super admin with full access
        if ($authUser instanceof \App\Models\Admin) {
            return true;
        }

        // Must be User model from here
        if (!($authUser instanceof \App\Models\User)) {
            // Unknown model type - allow access (fail-safe for super admins)
            return true;
        }

        // Get user_type
        $userType = $authUser->user_type ?? null;

        // Super admin or no user_type = full access
        if (!$userType || $userType === 'super_admin') {
            return true;
        }

        // Employer user can only access users in their organization
        if ($userType === 'employer_user') {
            return $targetUser->employer_id === $authUser->employer_id;
        }

        // Vendor user can only access users in their organization
        if ($userType === 'vendor_user') {
            return $targetUser->vendor_id === $authUser->vendor_id;
        }

        return false;
    }

    /**
     * Assign a role to the user.
     */
    public function assignRole(Request $request, string $id)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->assignRole($request->role);

        return response()->api(
            'Role assigned successfully',
            [
                'user' => new UserResource($user->load('roles')),
                'roles' => $user->getRoleNames(),
            ],
            200
        );
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole(Request $request, string $id)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = User::findOrFail($id);
        $user->removeRole($request->role);

        return response()->api(
            'Role removed successfully',
            [
                'user' => new UserResource($user->load('roles')),
                'roles' => $user->getRoleNames(),
            ],
            200
        );
    }

    /**
     * Get user roles.
     */
    public function getUserRoles(string $id)
    {
        $user = User::findOrFail($id);

        return response()->api(
            'User roles retrieved successfully',
            [
                'roles' => $user->getRoleNames(),
            ],
            200
        );
    }

    /**
     * Get user permissions.
     */
    public function getUserPermissions(string $id)
    {
        $user = User::findOrFail($id);

        return response()->api(
            'User permissions retrieved successfully',
            [
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            200
        );
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(string $id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        return response()->api(
            $user->is_active ? 'User activated successfully' : 'User deactivated successfully',
            new UserResource($user),
            200
        );
    }

    /**
     * Get subscription information for the authenticated user.
     */
    public function getMySubscription()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->api('Unauthenticated', null, 401);
        }

        // Admin users don't have subscriptions - they manage all
        if ($user instanceof \App\Models\Admin) {
            return response()->api(
                'Admin users do not have personal subscriptions. Use subscription management endpoints.',
                ['is_admin' => true],
                200
            );
        }

        $subscription = null;
        $ownerType = null;
        $userType = $user->user_type ?? null;

        if ($userType === 'employer_user') {
            $employer = $user->employer;
            $subscription = $employer?->activeSubscription()->with('package')->first();
            $ownerType = 'employer';
        } elseif ($userType === 'vendor_user') {
            $vendor = $user->vendor;
            $subscription = $vendor?->activeSubscription()->with('package')->first();
            $ownerType = 'vendor';
        } else {
            return response()->api(
                'Subscription information not applicable for this user type',
                null,
                404
            );
        }

        if (!$subscription) {
            return response()->api(
                'No active subscription found',
                ['has_subscription' => false],
                404
            );
        }

        return response()->api(
            'Subscription retrieved successfully',
            [
                'has_subscription' => true,
                'subscription' => [
                    'id' => $subscription->id,
                    'owner_type' => $ownerType,
                    'package' => [
                        'id' => $subscription->package->id,
                        'name' => $subscription->package->name,
                    ],
                    'seats' => [
                        'limit' => $subscription->seats_limit,
                        'used' => $subscription->seats_used,
                        'remaining' => $subscription->remaining_seats,
                        'usage_percentage' => round($subscription->seat_usage_percentage, 1),
                    ],
                    'can_add_users' => $subscription->hasAvailableSeats(),
                    'is_limit_reached' => $subscription->isSeatLimitReached(),
                    'status' => $subscription->status,
                    'started_at' => $subscription->started_at?->format('Y-m-d'),
                    'ends_at' => $subscription->ends_at?->format('Y-m-d'),
                ],
            ],
            200
        );
    }
}

