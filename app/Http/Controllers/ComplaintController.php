<?php

namespace App\Http\Controllers;

class ComplaintController extends Controller
{
    // private function getUserId()
    // {
    //     return auth()->user()->id;
    // }
    // public function index()
    // {
    //     return view('complaints.index');
    // }

    public function employeeComplaints()
    {


        $user = auth()->user();
        $roles = $user->getRoleNames(); // Collection of role names
        $permissions = $user->getAllPermissions()->pluck('name'); // Collection of permission names
    
        // return view('employee.complaints.index', ['user_id' => $this->getUserId()]);



        return view('employee.complaints.index', compact('user', 'roles', 'permissions'));

    }


}
