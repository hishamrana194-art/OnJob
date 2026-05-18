<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Return the permissions catalogue.
     *
     * | Caller          | Returned permissions          |
     * |-----------------|-------------------------------|
     * | Admin / no-type | All (system + non-system)     |
     * | tenant admin    | Non-system permissions only   |
     * | other users     | Non-system permissions only   |
     *
     * Response includes both a flat list and a grouped-by-category structure
     * so the frontend can render checkbox groups without extra processing.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Permission::class);

        $caller = auth()->user();

        $query = Permission::orderBy('name');

        // Tenant users may only see permissions they are permitted to assign
        $isPrivileged = ($caller instanceof Admin)
            || ($caller instanceof User && empty($caller->user_type));

        if (!$isPrivileged) {
            $query->where('is_system', false);
        }

        $permissions = $query->get();

        // Build a grouped map keyed by dot-prefix (e.g. "courses", "users")
        $grouped = [];
        foreach ($permissions as $permission) {
            $group = explode('.', $permission->name)[0] ?? 'other';
            $grouped[$group][] = new PermissionResource($permission);
        }
        ksort($grouped);

        return response()->api('Permissions retrieved successfully', [
            'permissions' => PermissionResource::collection($permissions),
            'grouped'     => $grouped,
        ], 200);
    }
}
