<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class DesignationController extends Controller
{

    public function index()
    {

        return view('designations.index');
    }

}
