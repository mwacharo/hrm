<?php

namespace App\Http\Controllers;

class AttendanceController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {

        return view('attendances.index');
    }

    public function employeeAttendances()
    {

        return view('employee.attendances.index', ['user_id' => $this->getUserId()]);
    }

    public function analytics()
    {

        return view('attendances.analytics', ['user_id' => $this->getUserId()]);
    }

}
