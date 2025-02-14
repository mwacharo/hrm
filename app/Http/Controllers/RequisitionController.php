<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequisitionRequest;
use App\Http\Requests\UpdateRequisitionRequest;
use App\Models\Requisition;
use Illuminate\Support\Facades\Auth;

class RequisitionController extends Controller
{

    public function index()
    {
       
    $user = auth()->user();
    $roles = $user->getRoleNames(); // Collection of role names
    $permissions = $user->getAllPermissions()->pluck('name'); // Collection of permission names

    // Debugging: Dump the data before returning the view
    // dd([
    //     'user' => $user,
    //     'roles' => $roles,
    //     'permissions' => $permissions
    // ]);

    return view('requisitions.requisitions', compact('user', 'roles', 'permissions'));

    }
}
