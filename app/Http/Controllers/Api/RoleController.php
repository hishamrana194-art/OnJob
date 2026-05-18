<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * List roles scoped to the caller's tenant.
     *
     * | Caller          | Returned roles                                |
     * |-----------------|-----------------------------------------------|
     * | Admin / no-type | All roles (system + every tenant)             |
     * | employer_admin  | System roles + this employer's custom roles   |
     * | vendor_admin    | System roles + this vendor's custom roles     |
     * | other users     | System roles only                             |
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::with('permissions')
            ->forUser(auth()->user())
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        return response()->api('Roles retrieved successfully', RoleResource::collection($roles), 200);
    }

    /**
     * Create a new tenant role.
     *
     * The role's employer_id / vendor_id is automatically set from the
     * authenticated user — callers cannot override it.
     * `is_system` can never be set via the API.
     */
    public function store(StoreRoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $caller = auth()->user();
        $data   = $request->validated();

        [$employerId, $vendorId] = $this->resolveTenantIds($caller);

        $role = Role::create([
            'name'        => $data['name'],
            'guard_name'  => $data['guard_name'] ?? 'web',
            'employer_id' => $employerId,
            'vendor_id'   => $vendorId,
        ]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($this->filterAssignablePermissions($data['permissions'], $caller));
        }

        $role->load('permissions');

        return response()->api('Role created successfully', new RoleResource($role), 201);
    }

    /**
     * Update a tenant role's name and/or permission set.
     * System roles are rejected by RolePolicy::update().
     */
    public function update(UpdateRoleRequest $request, string $id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('update', $role);

        $caller = auth()->user();
        $data   = $request->validated();

        if (isset($data['name'])) {
            $role->update(['name' => $data['name']]);
        }

        if (array_key_exists('permissions', $data)) {
            $role->syncPermissions(
                $this->filterAssignablePermissions($data['permissions'] ?? [], $caller)
            );
        }

        $role->load('permissions');

        return response()->api('Role updated successfully', new RoleResource($role), 200);
    }

    /**
     * Delete a tenant role.
     * System roles are rejected by RolePolicy::delete().
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $this->authorize('delete', $role);

        $role->delete();

        return response()->api('Role deleted successfully', null, 200);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    /**
     * Return [employer_id, vendor_id] for the authenticated caller.
     * Admin model → [null, null] (platform-wide role).
     */
    private function resolveTenantIds($caller): array
    {
        if ($caller instanceof User) {
            if ($caller->user_type === 'employer_user') {
                return [(int) $caller->employer_id, null];
            }
            if ($caller->user_type === 'vendor_user') {
                return [null, (int) $caller->vendor_id];
            }
        }
        return [null, null];
    }

    /**
     * Tenant users may only assign non-system permissions.
     * Admin / platform users may assign any permission.
     */
    private function filterAssignablePermissions(array $names, $caller): array
    {
        if ($caller instanceof Admin || ($caller instanceof User && empty($caller->user_type))) {
            return $names; // full access
        }

        return Permission::whereIn('name', $names)
            ->where('is_system', false)
            ->pluck('name')
            ->toArray();
    }
}
