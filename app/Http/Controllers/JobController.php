<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class JobController extends Controller
{

    public function index()
    {
        return view('jobs.index');
    }

}
