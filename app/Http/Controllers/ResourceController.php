<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class ResourceController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;

        return view('resources.index', ['user_id' => $user_id]);
    }

}
