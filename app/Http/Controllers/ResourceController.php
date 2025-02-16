<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ResourceController extends Controller
{
    // public function index()
    // {
    //     // $user_id = auth()->user()->id;

    //     // return view('resources.index', ['user_id' => $user_id]);


    //     $user = Auth::user(); // Get the authenticated user

    //     // dd($user);

    //     return view('resources.index', [
    //         'user' => $user,
    //         'roles' => $user->getRoleNames(), // Get assigned roles
    //         'permissions' => $user->getAllPermissions()->pluck('name') // Get permissions as an array of names
    //     ]);

    // }



    public function index()
{
    $user = Auth::user(); // Get the authenticated user

    $data = [
        'user' => $user,
        'roles' => $user->getRoleNames(), // Get assigned roles
        'permissions' => $user->getAllPermissions()->pluck('name') // Get permissions as an array of names
    ];

    // dd($data); // Dump and die before rendering the view

    return view('resources.index', $data);
}


}
