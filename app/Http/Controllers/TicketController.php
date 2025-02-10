<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class TicketController extends Controller
{
    private function getUserId()
    {
        return auth()->user()->id;
    }

    public function index()
    {
        return view('tickets.index',['user_id' => $this->getUserId()]);
    }

    public function employeeTickets()
    {
        return view('employee.tickets.index', ['user_id' => $this->getUserId()]);
    }
}
