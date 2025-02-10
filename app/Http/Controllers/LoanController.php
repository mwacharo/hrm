<?php

namespace App\Http\Controllers;

class LoanController extends Controller
{
    public function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {
        return view('loans.index');
    }

    public function employeeLoans()
    {
        return view('employee.loans.index', ['user_id' => $this->getUserId()]);
    }
}
