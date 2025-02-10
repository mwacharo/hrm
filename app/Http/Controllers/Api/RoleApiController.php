<?php

namespace App\Http\Controllers\Api;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleApiController extends Controller
{
    public function index()
    {

        $roles = Role::all();

        return response()->json(['roles' => $roles]);
    }
}
