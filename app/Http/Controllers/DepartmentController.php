<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{

    public function index()
    {
        return view('departments.index');
    }

}
