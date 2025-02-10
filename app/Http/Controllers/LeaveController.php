<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        return view('leaves.index', compact('userId'));
    }

    public function leaveRequests()
    {
        $userId = auth()->id();

        return view('leaves.requests', compact('userId'));
    }

    public function leaveBalances()
    {
        $userId = auth()->id();

        return view('leaves.balances', compact('userId'));
    }

    public function stats()
    {

        return view('leaves.stats');
    }

    public function create()
    {
        return view('leaves.assign');
    }

    public function employeeLeaves()
    {
        $userId = auth()->user()->id;

        return view('employee.leaves.index', ['userId' => $userId]);
    }

    public function leaveHistory()
    {
        return view('employee.leaves.history');
    }

    public function leavePlans()
    {

        return view('employee.leaves.plans');
    }

    public function leaveSummary()
    {
        return view('leaves.summary');
    }

    public function leaveAnalytics()
    {
        return view('leaves.analytics');
    }

    public function teamLeaves()
    {
        $userId = auth()->user()->id;
        return view('leaves.team', ['userId' => $userId]);
    }


}
