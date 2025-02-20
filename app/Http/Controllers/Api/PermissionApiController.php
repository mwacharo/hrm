<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;

class PermissionApiController extends Controller
{

    public function index()
    {

        $permissions = Permission::all();

        return response()->json(['permissions' => $permissions]);
    }
    public function getUserPermissions($userId)
    {
        $user = User::findOrFail($userId);

        $allPermissions = Permission::all();

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        $permissionsWithSelection = $allPermissions->map(function ($permission) use ($userPermissions) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'selected' => in_array($permission->name, $userPermissions),
            ];
        });

        return response()->json(['userPermissions' => $permissionsWithSelection]);
    }

    public function updateUserPermissions($userId, Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->syncPermissions($request->input('permissions'));

        return response()->json(['message' => 'Permissions updated successfully']);
    }




    // user

    public function show($id)
    {
        $user = User::findOrFail($id);

        // Get direct permissions
        $directPermissions = $user->permissions;

        // Get permissions via roles
        $rolePermissions = $user->getPermissionsViaRoles();

        // Merge them into one collection
        $allPermissions = $directPermissions->merge($rolePermissions)->pluck('name')->unique();

        return response()->json($allPermissions);
    }


    // user
    public function update(Request $request, $id)
    {

        Log::info('User ID received in controller:', ['id' => $id]); // Log the ID received

        $user = User::findOrFail($id);
        $permissions = $request->input('permissions');
        $user->syncPermissions($permissions);
        return response()->json(['message' => 'Permissions updated successfully']);
    }


    // Role 
    public function updateRolePermissions(Request $request, $roleId)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'permissions' => 'array', // Expect an array of permission names
            'permissions.*' => 'string|exists:permissions,name', // Each permission must be a string and exist in the database
        ]);

        // Find the role by ID
        $role = Role::findById($roleId);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Sync the permissions
        $role->syncPermissions($validated['permissions']);

        return response()->json([
            'message' => 'Permissions updated successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'), // Return the updated permissions
        ]);
    }



    /**
     * Get permissions for a specific role.
     */
    public function getRolePermissions($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $role->permissions;

        return response()->json(['permissions' => $permissions->pluck('name')]);
    }



    /**
     * Create a new role.
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'),
        ]);
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Request $request, $roleId)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $roleId,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $role = Role::findOrFail($roleId);
        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name'),
        ]);
    }

    /**
     * Create a new permission.
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Permission created successfully',
            'permission' => $permission->name,
        ]);
    }

    /**
     * Update an existing permission.
     */
    public function updatePermission(Request $request, $permissionId)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permissionId,
        ]);

        $permission = Permission::findOrFail($permissionId);
        $permission->update(['name' => $validated['name']]);

        return response()->json([
            'message' => 'Permission updated successfully',
            'permission' => $permission->name,
        ]);
    }



    /**
     * Remove the specified role from storage.
     */
    public function destroyRole($roleId)
    {
        // Find the role by ID
        $role = Role::findById($roleId);

        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Delete the role
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }


    /**
     * Remove the specified permission from storage.
     */
    public function destroyPermission($permissionId)
    {
        // Find the permission by ID
        $permission = Permission::findById($permissionId);

        if (!$permission) {
            return response()->json(['error' => 'Permission not found'], 404);
        }

        // Delete the permission
        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
