<?php

namespace App\Http\Controllers;


class LeaveTypeController extends Controller
{
    public function index()
    {
        return view('leave_types.index');
    }

}
