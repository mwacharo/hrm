<?php

namespace App\Http\Controllers;

class ReportController extends Controller
{
    public function leave()
    {
        return view('reports.leaves');

    }

    public function attendance()
    {
        return view('reports.attendances');
    }
}
