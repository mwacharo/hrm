<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
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

}
