<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserAccountController extends Controller
{
    public function index()
    {
        $user = User::with(['unit', 'department', 'designation','office'])->find(auth()->id());
        return view('account.index', ['user' => $user]);
    }
}
