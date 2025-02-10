<?php

namespace App\Http\Controllers;

class ComplaintController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }
    public function index()
    {
        return view('complaints.index');
    }

    public function employeeComplaints()
    {
        return view('employee.complaints.index', ['user_id' => $this->getUserId()]);
    }

}
