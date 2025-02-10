<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {

        return view('notifications.index');
    }

    public function employeeNofications()
    {

        return view('employee.notifications.index', ['user_id' => $this->getUserId()]);
    }

}
