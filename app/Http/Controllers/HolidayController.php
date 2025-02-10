<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


class HolidayController extends Controller
{

    public function index()
    {

        return view('holidays.index');
    }

}
