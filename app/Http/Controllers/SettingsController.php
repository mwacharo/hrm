<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {
        return view('settings.index', ['user_id' => $this->getUserId()]);
    }

    public function employeeSettings()
    {
        return view('employee.settings.index', ['user_id' => $this->getUserId()]);
    }

}
