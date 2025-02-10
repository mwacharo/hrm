<?php

namespace App\Http\Controllers;

class TimelineController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {
        return view('timeline.index');

    }
    public function employeeTimeline()
    {
        return view('employee.timeline.index', ['user_id' => $this->getUserId()]);

    }
}
