<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PolicyController extends Controller
{

    public function index()
    {
        return view('policies.index');
    }
}
